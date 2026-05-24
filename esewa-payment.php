<?php
include 'includes/header.php';

$p_id = $_GET['package_id'] ?? null;

$data = $_SESSION['booking_data'];

$package_id = $data['package_id'];

if (!isset($_SESSION['booking_data'])) {
    header("Location: booking?id=$p_id&error=required");
    exit;
}

$pid = "BOOK_" . time();
$_SESSION['pid'] = $pid;

$secret_key = "8gBm/:&EnhH.1/q"; 

$subtotal = $data['amount'] * $data['persons'];

if ($data['persons'] >= 5 && $data['persons'] < 10) {
    $subtotal *= 0.9; // 10% discount
}

if ($data['persons'] >= 10 && $data['persons'] <= 15) {
    $subtotal *= 0.8; // 20% discount
}

$tax_amount = 10;

$total_amount = $subtotal + $tax_amount;

$total_amount = round($total_amount, 2);

$product_code = "EPAYTEST";

$payload = "total_amount=$total_amount,transaction_uuid=$pid,product_code=$product_code";

$signature = base64_encode(hash_hmac('sha256', $payload, $secret_key, true));

?>

<body>
    <form action="https://rc-epay.esewa.com.np/api/epay/main/v2/form" method="POST">
        <input type="text" id="amount" name="amount" value="<?php echo $subtotal; ?>" required>
        <input type="text" id="tax_amount" name="tax_amount" value="<?php echo $tax_amount; ?>" required>
        <input type="text" id="total_amount" name="total_amount" value="<?php echo $total_amount; ?>" required>
        <input type="text" id="transaction_uuid" name="transaction_uuid" value="<?php echo $pid; ?>" required>
        <input type="text" id="product_code" name="product_code" value="<?php echo $product_code; ?>" required>
        <input type="text" id="product_service_charge" name="product_service_charge" value="0" required>
        <input type="text" id="product_delivery_charge" name="product_delivery_charge" value="0" required>
        <input type="text" id="success_url" name="success_url" value="http://localhost/Digital_Tourism_Platform/esewa-success" required>
        <input type="text" id="failure_url" name="failure_url" value="http://localhost/Digital_Tourism_Platform/esewa-fail" required>
        <input type="text" id="signed_field_names" name="signed_field_names" value="total_amount,transaction_uuid,product_code" required>
        <input type="text" id="signature" name="signature" value="<?php echo $signature ?>" required>
        <input value="Submit" type="submit">
    </form>
</body>


<script>
    document.forms[0].submit();
</script>
