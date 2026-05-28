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

function getRecommendations($conn, $current_tour_id, $limit = 6)
{

  $user_id = $_SESSION['user_id'] ?? null;

  if ($user_id) {

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
    $pref = $stmt->get_result()->fetch_assoc();

    $preferred_type = $pref['type'] ?? null;

    // 2. Hybrid query (user + collaborative + related)
    $stmt = $conn->prepare("
            SELECT t.*, COUNT(pb2.id) as popularity

            FROM tours t

            LEFT JOIN package_bookings pb2 
                ON t.id = pb2.package_id

            WHERE t.id != ?
            AND t.status = 1
            AND (
                t.type = ? 
                OR t.id IN (
                    SELECT pb2.package_id
                    FROM package_bookings pb1
                    JOIN package_bookings pb2 
                        ON pb1.user_id = pb2.user_id
                    WHERE pb1.package_id = ?
                )
            )

            GROUP BY t.id
            ORDER BY popularity DESC, t.is_popular DESC, t.created_at DESC
            LIMIT ?
        ");

    $stmt->bind_param("isii", $current_tour_id, $preferred_type, $current_tour_id, $limit);
    $stmt->execute();
    return $stmt->get_result();
  } else {

    $stmt = $conn->prepare("SELECT type, price FROM tours WHERE id=?");
    $stmt->bind_param("i", $current_tour_id);
    $stmt->execute();
    $tour = $stmt->get_result()->fetch_assoc();

    $type = $tour['type'];
    $price = $tour['price'];

    // Related + popular
    $stmt = $conn->prepare("
            SELECT t.*, COUNT(pb.id) as popularity

            FROM tours t
            LEFT JOIN package_bookings pb ON t.id = pb.package_id

            WHERE t.id != ?
            AND t.status = 1
            AND t.type = ?
            AND t.price BETWEEN ? - 5000 AND ? + 5000

            GROUP BY t.id
            ORDER BY popularity DESC, t.is_popular DESC, t.created_at DESC
            LIMIT ?
        ");

    $stmt->bind_param("isiii", $current_tour_id, $type, $price, $price, $limit);
    $stmt->execute();
    return $stmt->get_result();
  }
}
