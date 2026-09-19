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

if (
    $package_id !== $p_id
    || !isset($data['amount']) || !is_numeric($data['amount'])
    || !in_array($payment_option, ['full', 'deposit'], true)
) {
    header("Location: ../booking?trip=$slug&type=" . urlencode($data['pckg_type']) . "&price=" . urlencode($data['pckg_price']) . "&id=" . urlencode($data['package_id']) . "&error=required");
    exit;
}

$env = parse_ini_file(__DIR__ . '/../.env');
$secretKey = trim($env['KHALTI_SECRET_KEY']);
$appEnv = $env['APP_ENV'] ?? 'production';

// Sandbox vs live base URL - swap both this and the secret key together
// when you're ready to go live (see .env.example).
$apiBase = ($appEnv === 'production') ? 'https://khalti.com' : 'https://dev.khalti.com';

// ---------------------------------------------------------------------------
// Full package price (after group discount), computed server-side from the
// REAL per-person price stored in booking_data.
// ---------------------------------------------------------------------------
$pricing = calculateBookingTotal((float)$data['amount'], (int)$data['persons']);
$fullPackageTotal = $pricing['total_amount'];

$amountToChargeNow = ($payment_option === 'deposit')
    ? calculateDepositAmount($fullPackageTotal)
    : $fullPackageTotal;

// Khalti requires a minimum of Rs 10 and expects the amount in PAISA
// (rupees x 100), not rupees.
if ($amountToChargeNow < 10) {
    header("Location: ../booking?trip=$slug&type=" . urlencode($data['pckg_type']) . "&price=" . urlencode($data['pckg_price']) . "&id=" . urlencode($data['package_id']) . "&error=required");
    exit;
}
$amountPaisa = (int)round($amountToChargeNow * 100);

$purchaseOrderId = "BOOK_" . bin2hex(random_bytes(8)) . "_" . time();

$scheme = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$returnUrl = "$scheme://$host/Digital_Tourism_Platform/payment/khalti-success";
$websiteUrl = "$scheme://$host/Digital_Tourism_Platform";

$payload = [
    "return_url" => $returnUrl,
    "website_url" => $websiteUrl,
    "amount" => $amountPaisa,
    "purchase_order_id" => $purchaseOrderId,
    "purchase_order_name" => "Booking for package #$package_id (" . ($payment_option === 'deposit' ? '10% deposit' : 'full payment') . ")",
    "customer_info" => [
        "name" => $data['name'],
        "email" => $data['email'],
        "phone" => preg_replace('/[^0-9]/', '', $data['phone']),
    ],
];

$ch = curl_init("$apiBase/api/v2/epayment/initiate/");
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => [
        "Authorization: key $secretKey",
        "Content-Type: application/json",
    ],
    CURLOPT_POSTFIELDS => json_encode($payload),
    CURLOPT_TIMEOUT => 15,
]);

$response = curl_exec($ch);
$curlError = curl_error($ch);
curl_close($ch);

if ($curlError || !$response) {
    error_log("Khalti initiate call failed: $curlError");
    header("Location: khalti-fail?reason=initiate_failed");
    exit;
}

$result = json_decode($response, true);

if (!isset($result['pidx']) || !isset($result['payment_url'])) {
    error_log("Khalti initiate response missing pidx/payment_url: " . $response);
    header("Location: khalti-fail?reason=initiate_failed");
    exit;
}

// Persist exactly what we expect back, entirely server-generated - the
// upcoming khalti-success.php verifies against THIS, not anything Khalti's
// redirect claims on its own.
$_SESSION['khalti_expected'] = [
    'pidx' => $result['pidx'],
    'purchase_order_id' => $purchaseOrderId,
    'amount_paisa' => $amountPaisa,
    'full_package_total' => $fullPackageTotal,
    'amount_charged_now' => $amountToChargeNow,
    'payment_option' => $payment_option,
];

header("Location: " . $result['payment_url']);
exit;
