<?php

function getRecommendations($conn, $current_tour_id, $limit = 6)
{
    $user_id = $_SESSION['user_id'] ?? null;

    // ==========================
    // 🔐 LOGGED-IN USER
    // ==========================
    if ($user_id) {

        // ✅ 1. MOST VIEWED PACKAGE (frequency)
        $stmt = $conn->prepare("
            SELECT package_id, MAX(view_count) as views
            FROM user_activity
            WHERE user_id = ? AND action = 'view'
            GROUP BY package_id
            ORDER BY views DESC
            LIMIT 1
        ");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $viewed = $stmt->get_result()->fetch_assoc();
        $most_viewed_package = $viewed['package_id'] ?? 0;

        // details of the most-viewed package
        $stmt = $conn->prepare("
            SELECT t.*
            FROM tours t
            JOIN user_activity ua ON t.id = ua.package_id
            WHERE ua.user_id = ?
            ORDER BY ua.view_count DESC
            LIMIT 1
        ");

        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $refTour = $stmt->get_result()->fetch_assoc();

        $refPrice = $refTour['price'] ?? 0;
        $refDuration = $refTour['duration'] ?? '';

        // ✅ 2. MOST TIME SPENT PACKAGE
        $stmt = $conn->prepare("
            SELECT package_id, time_spent as total_time
            FROM user_activity
            WHERE user_id = ?
            ORDER BY time_spent DESC
            LIMIT 1
        ");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $time = $stmt->get_result()->fetch_assoc();
        $time_based_package = $time['package_id'] ?? 0;

        // time-spent package as another reference for price/duration similarity
        $stmt = $conn->prepare("
            SELECT t.*
            FROM tours t
            JOIN user_activity ua ON t.id = ua.package_id
            WHERE ua.user_id = ?
            ORDER BY ua.time_spent DESC
            LIMIT 1
        ");

        $stmt->bind_param("i", $user_id);
        $stmt->execute();

        $timeTour = $stmt->get_result()->fetch_assoc();

        $timePrice = $timeTour['price'] ?? 0;
        $timeDuration = $timeTour['duration'] ?? '';

        // ✅ 3. USER BOOKED PACKAGE
        $stmt = $conn->prepare("
            SELECT package_id, COUNT(*) as total
            FROM package_bookings
            WHERE user_id = ?
            GROUP BY package_id
            ORDER BY total DESC
            LIMIT 1
        ");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $book = $stmt->get_result()->fetch_assoc();
        $booked_package = $book['package_id'] ?? 0;

        $stmt = $conn->prepare("
            SELECT *
            FROM tours
            WHERE id = ?
        ");

        $stmt->bind_param("i", $booked_package);
        $stmt->execute();

        $bookTour = $stmt->get_result()->fetch_assoc();

        $bookPrice = $bookTour['price'] ?? 0;
        $bookDuration = $bookTour['duration'] ?? '';

        // ✅ 4. CURRENT TOUR PRICE
        $stmt = $conn->prepare("SELECT price FROM tours WHERE id=?");
        $stmt->bind_param("i", $current_tour_id);
        $stmt->execute();
        $tour = $stmt->get_result()->fetch_assoc();
        $price = $tour['price'] ?? 0;

        // ==========================
        // 🚀 FINAL SCORING QUERY
        // ==========================
        $stmt = $conn->prepare("
            SELECT t.*,

            (
                -- 🎯 SIMILAR TO MOST VIEWED
                -- (CASE WHEN t.id = ? THEN 10 ELSE 0 END)

                -- Similar price to most viewed package
                (CASE
                    WHEN t.price BETWEEN ? - 5000 AND ? + 5000
                    THEN 10
                    ELSE 0
                END)

                +

                -- Similar duration
                (CASE
                    WHEN t.duration = ?
                    THEN 8
                    ELSE 0
                END)

                +

                -- ⏱ TIME SPENT INTEREST
                -- (CASE WHEN t.id = ? THEN 12 ELSE 0 END)

                (CASE
                    WHEN t.price BETWEEN ? - 5000 AND ? + 5000
                    THEN 12
                    ELSE 0
                END)

                +

                (CASE
                    WHEN t.duration = ?
                    THEN 10
                    ELSE 0
                END)

                +

                -- 🧾 USER BOOKED RELATED
                -- (CASE WHEN t.id = ? THEN 15 ELSE 0 END)

                -- 🧾 USER BOOKED RELATED

                (CASE
                    WHEN t.price BETWEEN ? - 5000 AND ? + 5000
                    THEN 15
                    ELSE 0
                END)

                +

                (CASE
                    WHEN t.duration = ?
                    THEN 12
                    ELSE 0
                END)

                +

                -- 🤝 COLLABORATIVE FILTERING
                (CASE WHEN t.id IN (
                    SELECT pb2.package_id
                    FROM package_bookings pb1
                    JOIN package_bookings pb2
                    ON pb1.user_id = pb2.user_id
                    WHERE pb1.package_id = ?
                ) THEN 8 ELSE 0 END)

                +

                -- 💰 PRICE SIMILARITY
                (CASE WHEN t.price BETWEEN ? - 5000 AND ? + 5000 THEN 5 ELSE 0 END)

                +

                -- 🔥 POPULARITY
                (COUNT(pb.id) * 2)

                +

                -- 🆕 RECENT BOOST
                (CASE WHEN t.created_at >= NOW() - INTERVAL 7 DAY THEN 2 ELSE 0 END)

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
            "iisiisisiiiiii",

            $refPrice,
            $refPrice,
            $refDuration,

            $timePrice,
            $timePrice,
            $timeDuration,

            $bookPrice,
            $bookPrice,
            $bookDuration,

            $current_tour_id,

            $price,
            $price,

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

        $stmt = $conn->prepare("SELECT price FROM tours WHERE id=?");
        $stmt->bind_param("i", $current_tour_id);
        $stmt->execute();
        $tour = $stmt->get_result()->fetch_assoc();
        $price = $tour['price'] ?? 0;

        $stmt = $conn->prepare("
            SELECT t.*,

            (
                -- 💰 PRICE SIMILARITY
                (CASE WHEN t.price BETWEEN ? - 5000 AND ? + 5000 THEN 5 ELSE 0 END)

                +

                -- 🔥 POPULARITY
                (COUNT(pb.id) * 2)

                +

                -- 🤝 COLLABORATIVE FILTERING
                (CASE WHEN t.id IN (
                    SELECT pb2.package_id
                    FROM package_bookings pb1
                    JOIN package_bookings pb2
                    ON pb1.user_id = pb2.user_id
                    WHERE pb1.package_id = ?
                ) THEN 6 ELSE 0 END)

                +

                -- 🆕 RECENT
                (CASE WHEN t.created_at >= NOW() - INTERVAL 7 DAY THEN 2 ELSE 0 END)

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
            "iiiii",
            $price,
            $price,
            $current_tour_id,
            $current_tour_id,
            $limit
        );

        $stmt->execute();
        return $stmt->get_result();
    }
}
