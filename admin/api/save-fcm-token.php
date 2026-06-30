<?php
ini_set('display_errors', 0);
error_reporting(E_ALL);
header('Content-Type: application/json');

session_start();

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$adminId = (int) $_SESSION['admin_id'];

require_once __DIR__ . '/../../config/db.php';

$input = json_decode(file_get_contents('php://input'), true);
$token = isset($input['token']) ? trim($input['token']) : '';

if (empty($token)) {
    echo json_encode(['status' => 'error', 'message' => 'No token provided']);
    exit;
}

try {
    // Remove this exact token if it exists under any admin
    // (handles case where same browser previously belonged to a different admin)
    $stmt = $conn->prepare("DELETE FROM admin_fcm_tokens WHERE token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();

    // Insert fresh — linked to this admin
    $stmt = $conn->prepare("INSERT INTO admin_fcm_tokens (admin_id, token) VALUES (?, ?)");
    $stmt->bind_param("is", $adminId, $token);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Token saved']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Insert failed: ' . $conn->error]);
    }
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
