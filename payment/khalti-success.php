<?php

/**
 * ============================================================================
 *  KHALTI RETURN HANDLER - verify-then-record, same principle as eSewa
 * ============================================================================
 *  Khalti redirects the browser back here with query params claiming a
 *  status - but query params are exactly as trustworthy as anything else
 *  a browser sends, i.e. not trustworthy at all on their own. Before
 *  recording ANYTHING, this calls Khalti's /lookup/ endpoint server-to-
 *  server to independently confirm the payment, exactly like the eSewa
 *  status-check API call in payment/esewa-success.php.
 * ============================================================================
 */
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/mailer.php';

if (!isset($_SESSION['booking_data']) || !isset($_SESSION['khalti_expected'])) {
    header("Location: ../tours?error=invalid");
    exit;
}

$data = $_SESSION['booking_data'];
$expected = $_SESSION['khalti_expected'];
$package_id = $data['package_id'];

function khaltiVerificationFailed($package_id)
{
    error_log("Khalti payment verification FAILED for package_id=$package_id, session=" . session_id());
    unset($_SESSION['booking_data'], $_SESSION['khalti_expected']);
    header("Location: khalti-fail?reason=verification_failed");
    exit;
}

$pidxFromRedirect = $_GET['pidx'] ?? '';

// The pidx in the return URL must at least match the one WE generated at
// initiate time - if it doesn't, this request has nothing to do with the
// transaction we're tracking.
if ($pidxFromRedirect === '' || $pidxFromRedirect !== $expected['pidx']) {
    khaltiVerificationFailed($package_id);
}

$env = parse_ini_file(__DIR__ . '/../.env');
$secretKey = trim($env['KHALTI_SECRET_KEY']);
$appEnv = $env['APP_ENV'] ?? 'production';
$apiBase = ($appEnv === 'production') ? 'https://khalti.com' : 'https://dev.khalti.com';

// ---------------------------------------------------------------------------
// Server-to-server verification - the step that actually matters. Even if
// someone crafted a fake return URL with a "Completed"-looking status,
// this call asks Khalti directly and only Khalti's own answer is trusted.
// ---------------------------------------------------------------------------
$ch = curl_init("$apiBase/api/v2/epayment/lookup/");
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => [
        "Authorization: key $secretKey",
        "Content-Type: application/json",
    ],
    CURLOPT_POSTFIELDS => json_encode(["pidx" => $expected['pidx']]),
    CURLOPT_TIMEOUT => 15,
]);

$response = curl_exec($ch);
$curlError = curl_error($ch);
curl_close($ch);

if ($curlError || !$response) {
    error_log("Khalti lookup call failed: $curlError");
    khaltiVerificationFailed($package_id);
}

$lookup = json_decode($response, true);

if (!$lookup || ($lookup['status'] ?? '') !== 'Completed') {
    // Covers Pending / Expired / "User canceled" / Refunded / a malformed
    // response - none of those should ever result in a recorded booking.
    khaltiVerificationFailed($package_id);
}

// The amount Khalti confirms it actually received must match EXACTLY what
// we asked for at initiate time (in paisa) - stops a mismatched/tampered
// transaction from being accepted just because SOME payment completed.
if ((int)($lookup['total_amount'] ?? -1) !== (int)$expected['amount_paisa']) {
    khaltiVerificationFailed($package_id);
}

// ---------------------------------------------------------------------------
// ALL CHECKS PASSED - safe to record the booking.
// ---------------------------------------------------------------------------
$transactionId = $lookup['transaction_id'] ?? $expected['pidx'];
$fullPackageTotal = $expected['full_package_total'];
$amountPaid = $expected['amount_charged_now'];
$paymentOption = $expected['payment_option'];
$paymentStatus = $paymentOption === 'deposit' ? 'partial' : 'fully_paid';

$stmt = $conn->prepare("
    INSERT INTO package_bookings
    (package_id, user_id, name, email, country, phone, travel_date, persons, total_amount, amount_paid, payment_type, payment_status, payment_method, transaction_id)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Khalti', ?)
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
    $transactionId
);

// SAFEGUARD: Khalti's own documentation notes verification can be called
// more than once under some network conditions. The UNIQUE KEY on
// transaction_id rejects a duplicate insert for the same transaction -
// treat that as "already recorded" rather than an error, and skip
// re-sending duplicate notification emails.
$alreadyRecorded = false;
try {
    $stmt->execute();
} catch (mysqli_sql_exception $e) {
    if ($e->getCode() === 1062) { // ER_DUP_ENTRY
        $alreadyRecorded = true;
    } else {
        throw $e;
    }
}

if ($alreadyRecorded) {
    unset($_SESSION['booking_data'], $_SESSION['khalti_expected']);
    header("Location: ../tour-details?trip=" . $data['package_slug'] . "&type=" . urlencode($data['pckg_type']) . "&success=booked");
    exit;
}

require_once __DIR__ . '/../includes/send_fcm_notification.php';
$customerName = $data['name'];
sendAdminNotification(
    '🧳 New Booking Received!',
    $customerName . ' booked a trip via Khalti (' . ($paymentOption === 'deposit' ? '10% deposit' : 'full payment') . ').',
    '../admin/inquiries.php'
);

$paymentLabel = $paymentOption === 'deposit'
    ? "Deposit Paid: NPR " . number_format($amountPaid, 2) . " (Balance Due: NPR " . number_format($fullPackageTotal - $amountPaid, 2) . ")"
    : "Full Amount Paid: NPR " . number_format($amountPaid, 2);

$adminsubject = "New Booking for Package ID: " . $data['package_id'];
$adminbody = "
        <h3>New Booking Received (Khalti)</h3>
        <p><strong>Package ID:</strong> " . htmlspecialchars($data['package_id']) . "</p>
        <p><strong>Name:</strong> " . htmlspecialchars($data['name']) . "</p>
        <p><strong>Email:</strong> " . htmlspecialchars($data['email']) . "</p>
        <p><strong>Phone:</strong> " . htmlspecialchars($data['phone']) . "</p>
        <p><strong>Travel Date:</strong> " . htmlspecialchars($data['date']) . "</p>
        <p><strong>Persons:</strong> " . htmlspecialchars($data['persons']) . "</p>
        <p><strong>" . $paymentLabel . "</strong></p>
        <p><strong>Transaction ID:</strong> " . htmlspecialchars($transactionId) . "</p>
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
        <p><strong>Transaction ID:</strong> " . htmlspecialchars($transactionId) . "</p>
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

unset($_SESSION['booking_data'], $_SESSION['khalti_expected']);

header("Location: ../tour-details?trip=" . $data['package_slug'] . "&type=" . urlencode($data['pckg_type']) . "&success=booked");
exit;
