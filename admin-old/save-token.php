<?php
include '../config/db.php';

$data = json_decode(file_get_contents("php://input"), true);
$token = mysqli_real_escape_string($conn, $data['token']);

if (!$token) exit;

// avoid duplicates
$check = mysqli_query($conn,
  "SELECT id FROM admin_fcm_tokens WHERE token='$token'"
);

if (mysqli_num_rows($check) == 0) {
  mysqli_query($conn,
    "INSERT INTO admin_fcm_tokens (token) VALUES ('$token')"
  );
}

echo json_encode(["status" => "ok"]);
