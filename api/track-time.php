<?php
session_start();
include '../config/db.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    exit;
}

if (isset($_SESSION['user_id'])) {

    $user_id = $_SESSION['user_id'];
    $package_id = intval($data['package_id'] ?? 0);
    $time_spent = intval($data['time_spent'] ?? 0);

    if ($package_id > 0 && $time_spent > 0) {

        $stmt = $conn->prepare("
            INSERT INTO user_activity
            (user_id, package_id, action, view_count, time_spent)

            VALUES (?, ?, 'view', 1, ?)

            ON DUPLICATE KEY UPDATE
                view_count = view_count + 1,
                time_spent = time_spent + VALUES(time_spent),
                last_viewed_at = NOW();
        ");

        $stmt->bind_param("iii", $user_id, $package_id, $time_spent);
        $stmt->execute();
    }
}
