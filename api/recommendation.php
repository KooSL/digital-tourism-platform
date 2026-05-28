<?php

// ==============================
// 📊 TRACK USER ACTIVITY (FIXED)
// ==============================
if (isset($_SESSION['user_id'])) {
  $uid = $_SESSION['user_id'];

  $stmt = $conn->prepare("
    INSERT INTO user_activity (user_id, package_id, action)
    VALUES (?, ?, 'view')
  ");
  $stmt->bind_param("ii", $uid, $id);
  $stmt->execute();
}


// ==============================
// 🎯 HYBRID RECOMMENDATION ENGINE
// ==============================
function getRecommendations($conn, $current_tour_id, $limit = 6)
{
  $user_id = $_SESSION['user_id'] ?? null;

  // ==========================
  // 🔐 LOGGED-IN USER
  // ==========================
  if ($user_id) {

    // 👉 Get user preferred type (weighted)
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

    // 👉 MOST viewed type (frequency-based)
    $stmt = $conn->prepare("
      SELECT t.type
      FROM user_activity ua
      JOIN tours t ON ua.package_id = t.id
      WHERE ua.user_id = ?
      AND ua.action = 'view'
      GROUP BY t.type
      ORDER BY COUNT(*) DESC
      LIMIT 1
    ");

    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $top = $stmt->get_result()->fetch_assoc();

    $most_viewed_type = $top['type'] ?? null;

    // 👉 Get current tour price
    $stmt = $conn->prepare("SELECT price FROM tours WHERE id=?");
    $stmt->bind_param("i", $current_tour_id);
    $stmt->execute();
    $tour = $stmt->get_result()->fetch_assoc();

    $price = $tour['price'] ?? 0;

    // ==========================
    // 🚀 SMART SCORE QUERY
    // ==========================
    $stmt = $conn->prepare("
      SELECT t.*,

      (
        -- user preference
        (CASE WHEN t.type = ? THEN 5 ELSE 0 END)

        +

        -- price similarity
        (CASE WHEN t.price BETWEEN ? - 5000 AND ? + 5000 THEN 3 ELSE 0 END)

        +

        -- collaborative filtering
        (CASE WHEN t.id IN (
            SELECT pb2.package_id
            FROM package_bookings pb1
            JOIN package_bookings pb2 
              ON pb1.user_id = pb2.user_id
            WHERE pb1.package_id = ?
        ) THEN 4 ELSE 0 END)

        +

        -- popularity
        (COUNT(pb.id) * 2)

        +

        -- recent boost
        (CASE WHEN t.created_at >= NOW() - INTERVAL 7 DAY THEN 1 ELSE 0 END)

        +

        -- MOST VIEWED TYPE BOOST 🔥 (NEW)
        (CASE WHEN t.type = ? THEN 7 ELSE 0 END)

      ) AS score

      FROM tours t
      LEFT JOIN package_bookings pb ON t.id = pb.package_id

      WHERE t.id != ?
      AND t.status = 1

      GROUP BY t.id
      ORDER BY score DESC
      LIMIT ?
    ");

    $stmt->bind_param(
      "ssiiiii",
      $preferred_type,    
      $most_viewed_type,   
      $price,
      $price,
      $current_tour_id,
      $current_tour_id,
      $limit
    );

    $stmt->execute();
    return $stmt->get_result();
  }

  // ==========================
  // 👥 GUEST USER
  // ==========================
  else {

    // 👉 Get current tour info
    $stmt = $conn->prepare("SELECT type, price FROM tours WHERE id=?");
    $stmt->bind_param("i", $current_tour_id);
    $stmt->execute();
    $tour = $stmt->get_result()->fetch_assoc();

    $type = $tour['type'];
    $price = $tour['price'];

    // 👉 Smart ranking (no user data)
    $stmt = $conn->prepare("
      SELECT t.*,

      (
        -- type match
        (CASE WHEN t.type = ? THEN 5 ELSE 0 END)

        +

        -- price similarity
        (CASE WHEN t.price BETWEEN ? - 5000 AND ? + 5000 THEN 3 ELSE 0 END)

        +

        -- popularity
        (COUNT(pb.id) * 2)

        +

        -- recent boost
        (CASE WHEN t.created_at >= NOW() - INTERVAL 7 DAY THEN 1 ELSE 0 END)

      ) AS score

      FROM tours t
      LEFT JOIN package_bookings pb ON t.id = pb.package_id

      WHERE t.id != ?
      AND t.status = 1

      GROUP BY t.id
      ORDER BY score DESC
      LIMIT ?
    ");

    $stmt->bind_param("siiiii", $type, $price, $price, $current_tour_id, $limit);
    $stmt->execute();
    return $stmt->get_result();
  }
}
