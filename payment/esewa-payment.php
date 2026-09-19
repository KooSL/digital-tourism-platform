<?php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/pricing.php';

// $p_id = intval($_GET['package_id']);
$slug = $_GET['slug'];

if (!isset($_GET['slug']) || empty($slug)) {
    header("Location: ../tours?error=invalid");
    exit;
}

$stmt = mysqli_prepare($conn, "SELECT * FROM tours WHERE slug=? AND status=1");
mysqli_stmt_bind_param($stmt, "s", $slug);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$tour = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);
$p_id = $tour['id'] ?? null;

$data = $_SESSION['booking_data'];
$package_id = $data['package_id'];
$payment_option = $data['payment_option'] ?? 'full';

if (!isset($_SESSION['booking_data'])) {
    header("Location: ../booking?trip=$slug&type=" . urlencode($data['pckg_type']) . "&price=" . urlencode($data['pckg_price']) . "&id=" . urlencode($data['package_id']) . "&error=required");
    exit;
}

// Defense in depth: make sure the booking_data actually belongs to the
// package_id in the URL, that the amount really was set server-side by
// booking.php (not something a client could inject), and that the payment
// option is one of the two valid choices.
if (
    $package_id !== $p_id
    || !isset($data['amount']) || !is_numeric($data['amount'])
    || !in_array($payment_option, ['full', 'deposit'], true)
) {
    header("Location: ../booking?trip=$slug&type=" . urlencode($data['pckg_type']) . "&price=" . urlencode($data['pckg_price']) . "&id=" . urlencode($data['package_id']) . "&error=required");
    exit;
}

$pid = "BOOK_" . bin2hex(random_bytes(8)) . "_" . time();
$_SESSION['pid'] = $pid;

// Secret key now comes from .env instead of being hardcoded in source -
// add ESEWA_SECRET_KEY=... to your .env (see .env.example). The value
// below is eSewa's PUBLIC sandbox/test secret and is fine for the RC/UAT
// endpoint, but must be replaced with your real merchant secret before
// going live on the production eSewa endpoint.
$env = parse_ini_file(__DIR__ . '/../.env');
$secret_key = trim($env['ESEWA_SECRET_KEY']);

// ---------------------------------------------------------------------------
// Full package price (after group discount), computed server-side from the
// REAL per-person price stored in booking_data - the client never gets to
// send an amount directly.
// ---------------------------------------------------------------------------
$pricing = calculateBookingTotal((float)$data['amount'], (int)$data['persons']);
$fullPackageTotal = $pricing['total_amount'];

// ---------------------------------------------------------------------------
// Decide how much to actually charge in THIS transaction based on the
// user's choice at booking time - full amount, or a 10% deposit.
// ---------------------------------------------------------------------------
if ($payment_option === 'deposit') {
    $amountToChargeNow = calculateDepositAmount($fullPackageTotal);
} else {
    $amountToChargeNow = $fullPackageTotal;
}

$tax_amount = 10;
$esewaTotal = $amountToChargeNow + $tax_amount; // what's actually signed/sent to eSewa for this transaction

$product_code = "EPAYTEST";

// Persist exactly what we expect back from eSewa so esewa-success.php can
// verify the callback matches what WE calculated, instead of trusting
// whatever the redirect happens to say. Also carries the FULL package
// total and the payment_option forward so esewa-success.php can record
// both "amount paid now" and "full price of the booking" accurately,
// without having to trust anything from the client again at that step.
$_SESSION['esewa_expected'] = [
    'total_amount' => $esewaTotal,
    'product_code' => $product_code,
    'transaction_uuid' => $pid,
    'full_package_total' => $fullPackageTotal,
    'amount_charged_now' => $amountToChargeNow,
    'payment_option' => $payment_option,
];

$payload = "total_amount=$esewaTotal,transaction_uuid=$pid,product_code=$product_code";
$signature = base64_encode(hash_hmac('sha256', $payload, $secret_key, true));

if (($env['APP_ENV'] ?? 'production') !== 'production') {
    // Never log $secret_key itself - only the payload it was signing and
    // the resulting signature, safe to compare against eSewa's dashboard
    // logs / support if a payment ever gets rejected with ES104.
    error_log("eSewa payload: $payload | signature: $signature | secret_key_length: " . strlen($secret_key));
}

$scheme = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];

?>

<body onload="document.forms[0].submit();">
    <form action="https://rc-epay.esewa.com.np/api/epay/main/v2/form" method="POST">
        <input type="hidden" name="amount" value="<?= htmlspecialchars($amountToChargeNow) ?>">
        <input type="hidden" name="tax_amount" value="<?= htmlspecialchars($tax_amount) ?>">
        <input type="hidden" name="total_amount" value="<?= htmlspecialchars($esewaTotal) ?>">
        <input type="hidden" name="transaction_uuid" value="<?= htmlspecialchars($pid) ?>">
        <input type="hidden" name="product_code" value="<?= htmlspecialchars($product_code) ?>">
        <input type="hidden" name="product_service_charge" value="0">
        <input type="hidden" name="product_delivery_charge" value="0">
        <input type="hidden" name="success_url" value="<?= htmlspecialchars("$scheme://$host/Digital_Tourism_Platform/payment/esewa-success") ?>">
        <input type="hidden" name="failure_url" value="<?= htmlspecialchars("$scheme://$host/Digital_Tourism_Platform/payment/esewa-fail") ?>">
        <input type="hidden" name="signed_field_names" value="total_amount,transaction_uuid,product_code">
        <input type="hidden" name="signature" value="<?= htmlspecialchars($signature) ?>">
        <noscript><button type="submit">Continue to eSewa</button></noscript>
    </form>
    <!-- <p class="redirect-message">
        Redirecting you to eSewa to complete your
        <?= $payment_option === 'deposit' ? 'deposit' : 'full' ?> payment of NPR <?= htmlspecialchars($amountToChargeNow) ?>...
    </p> -->
</body>