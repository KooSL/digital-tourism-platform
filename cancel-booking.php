<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: signin");
    exit;
}

$returnTo = $_POST['return_to'] ?? 'my-bookings';
// Only allow redirecting back to the two pages that can submit this form.
if (!preg_match('/^(my-bookings|booking-details\?slug=[^&]*&id=\d+)$/', $returnTo)) {
    $returnTo = 'my-bookings';
}
$sep = str_contains($returnTo, '?') ? '&' : '?';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: my-bookings");
    exit;
}

if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])) {
    die("CSRF validation failed.");
}

$user_id = $_SESSION['user_id'];
$booking_id = (int)($_POST['booking_id'] ?? 0);

$stmt = $conn->prepare("SELECT payment_status, status FROM package_bookings WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $booking_id, $user_id);
$stmt->execute();
$booking = $stmt->get_result()->fetch_assoc();

if (!$booking) {
    header("Location: $returnTo{$sep}error=not_found");
    exit;
}

if ($booking['payment_status'] !== 'pending' || $booking['status'] === 'canceled') {
    header("Location: $returnTo{$sep}error=cannot_cancel");
    exit;
}

$update = $conn->prepare("UPDATE package_bookings SET status = 'canceled' WHERE id = ? AND user_id = ?");
$update->bind_param("ii", $booking_id, $user_id);
$update->execute();

header("Location: $returnTo{$sep}success=canceled");
exit;
