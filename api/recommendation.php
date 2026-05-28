<?php

if (isset($_SESSION['user_id'])) {
    $uid = $_SESSION['user_id'];

    $stmt = $conn->prepare("
    INSERT INTO user_activity (user_id, package_id, action)
    VALUES (?, ?, 'viewed')
  ");
    $stmt->bind_param("ii", $uid, $id);
    $stmt->execute();
}

// $current_id = $tour['id'];
// $type = $tour['type'];
// $price = $tour['price'];

// $stmt = $conn->prepare("
//   SELECT * FROM tours
//   WHERE id != ?
//   AND status = 1
//   AND type = ?
//   ORDER BY is_popular DESC, created_at DESC
//   LIMIT 4
// ");

// $stmt->bind_param("is", $current_id, $type);
// $stmt->execute();
// $recommended = $stmt->get_result();

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("
  SELECT t.type,
SUM(CASE 
  WHEN ua.action = 'book' THEN 3
  WHEN ua.action = 'view' THEN 1
END) as score
FROM user_activity ua
JOIN tours t ON ua.package_id = t.id
WHERE ua.user_id = ?
GROUP BY t.type
ORDER BY score DESC
LIMIT 1
");

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$pref = $result->fetch_assoc();

$preferred_type = $pref['type'] ?? null;

if ($preferred_type) {

    $stmt = $conn->prepare("
    SELECT * FROM tours
    WHERE type = ?
    AND status = 1
    ORDER BY is_popular DESC, created_at DESC
    LIMIT 4
  ");

    $stmt->bind_param("s", $preferred_type);
    $stmt->execute();
    $recommended = $stmt->get_result();
} else {
    $recommended = [];
}


$stmt = $conn->prepare("
  SELECT pb2.package_id, COUNT(*) as total, t.*
  FROM package_bookings pb1

  JOIN package_bookings pb2 
    ON pb1.user_id = pb2.user_id 
    AND pb1.package_id != pb2.package_id

  JOIN tours t 
    ON pb2.package_id = t.id

  WHERE pb1.package_id = ?
  AND t.status = 1

  GROUP BY pb2.package_id
  ORDER BY total DESC
  LIMIT 4
");

$stmt->bind_param("i", $current_tour_id);
$stmt->execute();
$also_booked = $stmt->get_result();