<?php
include 'includes/header.php';
require_once __DIR__ . '/config/db.php';

$p_id = intval($_GET['package_id'] ?? 0);

if (!isset($_GET['package_id']) || $p_id <= 0) {
    header("Location: tours?error=invalid");
    exit;
}

if (!isset($_SESSION['booking_data'])) {
    header("Location: booking?id=$p_id&error=required");
    exit;
}

$data = $_SESSION['booking_data'];
$package_id = $data['package_id'];

// Defense in depth: make sure the booking_data actually belongs to the
// package_id in the URL, and that the amount really was set server-side
// by booking.php (not something a client could inject).
if ($package_id !== $p_id || !isset($data['amount']) || !is_numeric($data['amount'])) {
    header("Location: booking?id=$p_id&error=required");
    exit;
}

$pid = "BOOK_" . bin2hex(random_bytes(8)) . "_" . time();
$_SESSION['pid'] = $pid;

// Secret key now comes from .env instead of being hardcoded in source -
// add ESEWA_SECRET_KEY=... to your .env (see .env.example). The value
// below is eSewa's PUBLIC sandbox/test secret and is fine for the RC/UAT
// endpoint, but must be replaced with your real merchant secret before
// going live on the production eSewa endpoint.
$env = parse_ini_file(__DIR__ . '/.env');
$secret_key = $env['ESEWA_SECRET_KEY'] ?? '8gBm/:&EnhH.1/q';

$subtotal = round($data['amount'] * $data['persons'], 2);

if ($data['persons'] >= 5 && $data['persons'] < 10) {
    $subtotal = round($subtotal * 0.9, 2); // 10% group discount
}

if ($data['persons'] >= 10 && $data['persons'] <= 15) {
    $subtotal = round($subtotal * 0.8, 2); // 20% group discount
}

$tax_amount = 10;
$total_amount = round($subtotal + $tax_amount, 2);

$product_code = "EPAYTEST";

// Persist exactly what we expect back from eSewa so esewa-success.php can
// verify the callback matches what WE calculated, instead of trusting
// whatever the redirect happens to say.
$_SESSION['esewa_expected'] = [
    'total_amount' => $total_amount,
    'product_code' => $product_code,
    'transaction_uuid' => $pid,
];

$payload = "total_amount=$total_amount,transaction_uuid=$pid,product_code=$product_code";
$signature = base64_encode(hash_hmac('sha256', $payload, $secret_key, true));

?>

<body onload="document.forms[0].submit();">
    <form action="https://rc-epay.esewa.com.np/api/epay/main/v2/form" method="POST">
        <input type="hidden" name="amount" value="<?= htmlspecialchars($subtotal) ?>">
        <input type="hidden" name="tax_amount" value="<?= htmlspecialchars($tax_amount) ?>">
        <input type="hidden" name="total_amount" value="<?= htmlspecialchars($total_amount) ?>">
        <input type="hidden" name="transaction_uuid" value="<?= htmlspecialchars($pid) ?>">
        <input type="hidden" name="product_code" value="<?= htmlspecialchars($product_code) ?>">
        <input type="hidden" name="product_service_charge" value="0">
        <input type="hidden" name="product_delivery_charge" value="0">
        <input type="hidden" name="success_url" value="<?= htmlspecialchars((isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/Digital_Tourism_Platform/esewa-success') ?>">
        <input type="hidden" name="failure_url" value="<?= htmlspecialchars((isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/Digital_Tourism_Platform/esewa-fail') ?>">
        <input type="hidden" name="signed_field_names" value="total_amount,transaction_uuid,product_code">
        <input type="hidden" name="signature" value="<?= htmlspecialchars($signature) ?>">
        <noscript><button type="submit">Continue to eSewa</button></noscript>
    </form>
    <p class="esewa-redirect">Redirecting you to eSewa to complete your payment...</p>
</body>