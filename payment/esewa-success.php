<?php

/**
 * NOTE ON LOCATION: this file now lives in payment/esewa-success.php,
 * matching the success_url built in payment/esewa-payment.php. Includes
 * below are adjusted one level up (__DIR__ . '/../...') accordingly.
 */
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/mailer.php';

/**
 * ============================================================================
 *  CRITICAL FIX (kept from the original hardening pass): this page used to
 *  insert a 'paid' booking the instant a browser landed here - with no
 *  check that a payment ever actually happened. This version verifies the
 *  payment THREE ways (signature, status, and a server-to-server status
 *  check with eSewa) before recording anything.
 *
 *  UPDATED: now also records how much was actually paid vs the full
 *  package price, since a booking can be paid in full or via a 10%
 *  deposit - both amounts are read from $_SESSION['esewa_expected'],
 *  which is entirely server-generated (set in payment/esewa-payment.php),
 *  never from anything the client sent directly at this step.
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
$secret_key = trim($env['ESEWA_SECRET_KEY']);

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
// EXACTLY what we generated in payment/esewa-payment.php - not just be
// internally signature-consistent.
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
// ALL CHECKS PASSED - safe to record the booking.
//
// total_amount   = full package price (after group discount) - regardless
//                  of how much was actually charged in this transaction.
// amount_paid    = what was actually charged now (full amount, or the 10%
//                  deposit) - read from server-generated session data set
//                  at payment-init time, never from the client at this step.
// payment_type   = 'full' or 'deposit'.
// payment_status = 'paid' if the full amount was charged, 'partial' if
//                  only the deposit was - lets my-bookings.php show a
//                  distinct "Partially Paid" state instead of conflating
//                  it with a fully-paid booking.
// ---------------------------------------------------------------------------
$pid = $expected['transaction_uuid'];
$fullPackageTotal = $expected['full_package_total'] ?? $expected['total_amount'];
$amountPaid = $expected['amount_charged_now'] ?? $expected['total_amount'];
$paymentOption = $expected['payment_option'] ?? 'full';
$paymentStatus = $paymentOption === 'deposit' ? 'partial' : 'fully_paid';

$stmt = $conn->prepare("
    INSERT INTO package_bookings
    (package_id, user_id, name, email, country, phone, travel_date, persons, total_amount, amount_paid, payment_type, payment_status, payment_method, transaction_id)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'eSewa', ?)
");

$stmt->bind_param(
    "iisssssiddsss",
    $data['package_id'],
    $data['user_id'],
    $data['name'],
    $data['email'],
    $data['country'],
    $data['phone'],
    $data['date'],
    $data['persons'],
    $fullPackageTotal,
    $amountPaid,
    $paymentOption,
    $paymentStatus,
    $pid
);

// SAFEGUARD: if this exact transaction_id was already recorded (e.g. the
// user double-clicked, hit back-and-forward, or eSewa redelivered the
// callback), the UNIQUE KEY on transaction_id rejects the duplicate insert.
// That's expected and fine - it means the booking already exists from the
// first successful attempt, so just continue to the success page instead
// of treating it as an error (and without re-sending duplicate emails).
$alreadyRecorded = false;
try {
    $stmt->execute();
} catch (mysqli_sql_exception $e) {
    if ($e->getCode() === 1062) { // ER_DUP_ENTRY
        $alreadyRecorded = true;
    } else {
        throw $e; // any other DB error is a real problem, don't swallow it
    }
}

if ($alreadyRecorded) {
    unset($_SESSION['booking_data'], $_SESSION['pid'], $_SESSION['esewa_expected']);
    header("Location: ../tour-details?trip=" . urlencode($data['package_slug']) . "&type=" . urlencode($data['pckg_type']) . "&success=booked");
    exit;
}

require_once __DIR__ . '/../includes/send_fcm_notification.php';
$customerName = $data['name'];
sendAdminNotification(
    '🧳 New Booking Received!',
    $customerName . ' booked a trip (' . ($paymentOption === 'deposit' ? '10% deposit' : 'full payment') . ').',
    '../admin/inquiries.php'
);

$paymentLabel = $paymentOption === 'deposit'
    ? "Deposit Paid: NPR " . number_format($amountPaid, 2) . " (Balance Due: NPR " . number_format($fullPackageTotal - $amountPaid, 2) . ")"
    : "Full Amount Paid: NPR " . number_format($amountPaid, 2);

$adminsubject = "New Booking for Package ID: " . $data['package_id'];
$adminbody = "
        <h3>New Booking Received</h3>
        <p><strong>Package ID:</strong> " . htmlspecialchars($data['package_id']) . "</p>
        <p><strong>Name:</strong> " . htmlspecialchars($data['name']) . "</p>
        <p><strong>Email:</strong> " . htmlspecialchars($data['email']) . "</p>
        <p><strong>Phone:</strong> " . htmlspecialchars($data['phone']) . "</p>
        <p><strong>Travel Date:</strong> " . htmlspecialchars($data['date']) . "</p>
        <p><strong>Persons:</strong> " . htmlspecialchars($data['persons']) . "</p>
        <p><strong>" . $paymentLabel . "</strong></p>
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
        <p><strong>" . $paymentLabel . "</strong></p>
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

header("Location: ../tour-details?trip=" . urlencode($data['package_slug']) . "&type=" . urlencode($data['pckg_type']) . "&success=booked");
exit;
 