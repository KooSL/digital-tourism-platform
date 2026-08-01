<?php
include '../includes/header.php';

$data = $_SESSION['booking_data'];
$id = $data['package_id'];

unset($_SESSION['booking_data']);
unset($_SESSION['pid']);

header("Location: ../tour-details?id=$id&error=booking_failed");
exit;
