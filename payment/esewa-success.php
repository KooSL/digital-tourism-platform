<?php
include '../includes/header.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/mailer.php';

/**
 * ============================================================================
 *  CRITICAL FIX: this page used to insert a 'paid' booking the instant a
 *  browser landed here - with no check that a payment ever actually
 *  happened. Since $_SESSION['booking_data'] is fully under the visitor's
 *  control (set by submitting the booking form), ANYONE could get a free
 *  "paid" booking just by navigating straight to esewa-success.php.
 *
 *  This version verifies the payment THREE ways before recording anything:
 *   1. eSewa's own signature on the returned `data` payload (proves the
 *      response actually came from eSewa and wasn't forged/tampered with).
 *   2. status === "COMPLETE" in that payload.
 *   3. A server-to-server call to eSewa's transaction status-check API,
 *      which confirms with eSewa directly (not just trusting the redirect)
 *      that this transaction really was paid, for this exact amount.
 *
 *  Only if all three pass does a booking get written to the database.
 * ============================================================================
 */

if (!isset($_SESSION['booking_data']) || !isset($_SESSION['esewa_expected'])) {
    header("Location: ../tours?error=invalid");
    exit;
}

$data = $_SESSION['booking_data'];
$expected = $_SESSION['esewa_expected'];
$package_id = $data['package_id'];

function esewaVerificationFailed($package_id)
{
    error_log("eSewa payment verification FAILED for package_id=$package_id, session=" . session_id());
    unset($_SESSION['booking_data'], $_SESSION['pid'], $_SESSION['esewa_expected']);
    header("Location: esewa-fail?reason=verification_failed"); 
    exit;
}

// ---------------------------------------------------------------------------
// STEP 1: decode + verify the signed `data` payload eSewa redirected back with
// ---------------------------------------------------------------------------
$rawData = $_GET['data'] ?? '';
if (empty($rawData)) {
    esewaVerificationFailed($package_id);
}

$decoded = json_decode(base64_decode($rawData), true);
if (!$decoded || !isset($decoded['signature'], $decoded['signed_field_names'], $decoded['status'], $decoded['transaction_uuid'], $decoded['total_amount'])) {
    esewaVerificationFailed($package_id);
}

$env = parse_ini_file(__DIR__ . '/../.env');
$secret_key = $env['ESEWA_SECRET_KEY'];

// Rebuild the exact payload string eSewa signed, using the field order
// eSewa itself reports in signed_field_names - don't assume a fixed order.
$fields = explode(',', $decoded['signed_field_names']);
$payloadParts = [];
foreach ($fields as $field) {
    $payloadParts[] = $field . '=' . ($decoded[$field] ?? '');
}
$payload = implode(',', $payloadParts);
$expectedSignature = base64_encode(hash_hmac('sha256', $payload, $secret_key, true));

if (!hash_equals($expectedSignature, $decoded['signature'])) {
    esewaVerificationFailed($package_id); // signature mismatch -> forged/tampered response
}

if ($decoded['status'] !== 'COMPLETE') {
    esewaVerificationFailed($package_id);
}

// ---------------------------------------------------------------------------
// STEP 2: the transaction_uuid & total_amount in the response must match
// EXACTLY what we generated in esewa-payment.php - not just be internally
// signature-consistent. This stops someone from paying for a cheap package
// and replaying/adapting that valid signature onto a different/expensive
// booking.
// ---------------------------------------------------------------------------
if (
    $decoded['transaction_uuid'] !== $expected['transaction_uuid'] ||
    (float)$decoded['total_amount'] !== (float)$expected['total_amount'] ||
    $decoded['product_code'] !== $expected['product_code']
) {
    esewaVerificationFailed($package_id);
}

// ---------------------------------------------------------------------------
// STEP 3: server-to-server confirmation directly with eSewa's status API.
// This is the step that actually protects against a forged `data` param -
// even if steps 1-2 were somehow spoofed, eSewa's own servers won't confirm
// a transaction that never happened.
// ---------------------------------------------------------------------------
$statusUrl = "https://rc.esewa.com.np/api/epay/transaction/status/?"
    . http_build_query([
        'product_code'     => $expected['product_code'],
        'total_amount'     => $expected['total_amount'],
        'transaction_uuid' => $expected['transaction_uuid'],
    ]);

$ch = curl_init($statusUrl);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 10,
    CURLOPT_SSL_VERIFYPEER => true,
]);
$statusResponse = curl_exec($ch);
$curlError = curl_error($ch);
curl_close($ch);

if ($curlError || !$statusResponse) {
    error_log("eSewa status-check API call failed: $curlError");
    esewaVerificationFailed($package_id);
}

$statusData = json_decode($statusResponse, true);
if (!$statusData || ($statusData['status'] ?? '') !== 'COMPLETE') {
    esewaVerificationFailed($package_id);
}

// ---------------------------------------------------------------------------
// ALL CHECKS PASSED - safe to record the booking as paid.
// ---------------------------------------------------------------------------
$pid = $expected['transaction_uuid'];

$stmt = $conn->prepare("
    INSERT INTO package_bookings
    (package_id, user_id, name, email, country, phone, travel_date, persons, payment_status, payment_method, transaction_id)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'paid', 'eSewa', ?)
");

$stmt->bind_param(
    "iisssssis",
    $data['package_id'],
    $data['user_id'],
    $data['name'],
    $data['email'],
    $data['country'],
    $data['phone'],
    $data['date'],
    $data['persons'],
    $pid
);

$stmt->execute();

require_once __DIR__ . '/../includes/send_fcm_notification.php';
$customerName = $data['name'];
sendAdminNotification(
    '🧳 New Booking Received!',
    $customerName . ' booked a trip.',
    '../admin/inquiries.php'
);

$adminsubject = "New Booking for Package ID: " . $data['package_id'];
$adminbody = "
        <h3>New Booking Received</h3>
        <p><strong>Package ID:</strong> " . htmlspecialchars($data['package_id']) . "</p>
        <p><strong>Name:</strong> " . htmlspecialchars($data['name']) . "</p>
        <p><strong>Email:</strong> " . htmlspecialchars($data['email']) . "</p>
        <p><strong>Phone:</strong> " . htmlspecialchars($data['phone']) . "</p>
        <p><strong>Travel Date:</strong> " . htmlspecialchars($data['date']) . "</p>
        <p><strong>Persons:</strong> " . htmlspecialchars($data['persons']) . "</p>
        <p><strong>Transaction ID:</strong> " . htmlspecialchars($pid) . "</p>
    ";
sendAdminMail($adminsubject, $adminbody);

$usersubject = "New Booking for Package ID: " . $data['package_id'] . " - Confirmation";
$userbody = "
        <h3>New Booking Received</h3>
        <p><strong>Package ID:</strong> " . htmlspecialchars($data['package_id']) . "</p>
        <p><strong>Name:</strong> " . htmlspecialchars($data['name']) . "</p>
        <p><strong>Email:</strong> " . htmlspecialchars($data['email']) . "</p>
        <p><strong>Phone:</strong> " . htmlspecialchars($data['phone']) . "</p>
        <p><strong>Travel Date:</strong> " . htmlspecialchars($data['date']) . "</p>
        <p><strong>Persons:</strong> " . htmlspecialchars($data['persons']) . "</p>
        <p><strong>Transaction ID:</strong> " . htmlspecialchars($pid) . "</p>
    ";
sendUserMail($data['email'], $usersubject, $userbody);

if (!empty($data['user_id'])) {
    $stmt = $conn->prepare("
      INSERT INTO user_activity (user_id, package_id, action)
      VALUES (?, ?, 'book')
        ON DUPLICATE KEY UPDATE action = 'book';
    ");
    $stmt->bind_param("ii", $data['user_id'], $package_id);
    $stmt->execute();
}

unset($_SESSION['booking_data'], $_SESSION['pid'], $_SESSION['esewa_expected']);

header("Location: ../tour-details?id=" . $data['package_id'] . "&success=booked");
exit;
