<?php
include '../includes/header.php';

$reason = $_GET['reason'] ?? 'booking_failed';

$data = $_SESSION['booking_data'];
$id = $data['package_id'];
$slug = $data['package_slug'];

unset($_SESSION['booking_data']);
unset($_SESSION['pid']);
unset($_SESSION['khalti_expected']);

header("Location: ../booking?trip=$slug&type=" . urlencode($data['pckg_type']) . "&price=" . urlencode($data['pckg_price']) . "&id=" . urlencode($data['package_id']) . "&error=$reason");
exit;
