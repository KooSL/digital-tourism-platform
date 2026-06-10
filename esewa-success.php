<?php
include 'includes/header.php';
include 'config/db.php';
include 'includes/mailer.php';

$data = $_SESSION['booking_data'];
$package_id = $data['package_id'];

if (!isset($_SESSION['booking_data'])) {
    header("Location: tours?error=invalid");
}

$pid = $_SESSION['pid'] ?? '';


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

$adminsubject = "New Booking for Package ID: " . $data['package_id'];
$adminbody = "
        <h3>New Booking Received</h3>
        <p><strong>Package ID:</strong> " . $data['package_id'] . "</p>
        <p><strong>Name:</strong> " . $data['name'] . "</p>
        <p><strong>Email:</strong> " . $data['email'] . "</p>
        <p><strong>Phone:</strong> " . $data['phone'] . "</p>
        <p><strong>Travel Date:</strong> " . $data['date'] . "</p>
        <p><strong>Persons:</strong> " . $data['persons'] . "</p>
        <p><strong>Transaction ID:</strong> " . $pid . "</p>
    ";
sendAdminMail($adminsubject, $adminbody);

$usersubject = "New Booking for Package ID: " . $data['package_id'] . " - Confirmation";
$userbody = "
        <h3>New Booking Received</h3>
        <p><strong>Package ID:</strong> " . $data['package_id'] . "</p>
        <p><strong>Name:</strong> " . $data['name'] . "</p>
        <p><strong>Email:</strong> " . $data['email'] . "</p>
        <p><strong>Phone:</strong> " . $data['phone'] . "</p>
        <p><strong>Travel Date:</strong> " . $data['date'] . "</p>
        <p><strong>Persons:</strong> " . $data['persons'] . "</p>
        <p><strong>Transaction ID:</strong> " . $pid . "</p>
    ";
sendUserMail($data['email'], $usersubject, $userbody);

$stmt = $conn->prepare("
  INSERT INTO user_activity (user_id, package_id, action)
  VALUES (?, ?, 'book')
    ON DUPLICATE KEY UPDATE action = 'book';
");
$stmt->bind_param("ii", $_SESSION['user_id'], $package_id);
$stmt->execute();


unset($_SESSION['booking_data']);
unset($_SESSION['pid']);

header("Location: tour-details?id=" . $data['package_id'] . "&success=booked");
exit;
