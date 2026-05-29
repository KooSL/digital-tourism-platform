<?php
session_start();
include 'config/db.php';

$data = json_decode(file_get_contents("php://input"), true);

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $package_id = intval($data['package_id']);
    $time_spent = intval($data['time_spent']);

    $stmt = $conn->prepare("
        UPDATE user_activity
        SET time_spent = time_spent + ?
        WHERE user_id = ? AND package_id = ? AND action = 'view'
    ");

    $stmt->bind_param("iii", $time_spent, $user_id, $package_id);
    $stmt->execute();
}
