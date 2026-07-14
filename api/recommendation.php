<?php

function getRecommendations($conn, $current_tour_id, $limit = 5)
{

    $user_id = $_SESSION['user_id'] ?? null;


    /*
|--------------------------------------------------------------------------
| CURRENT TOUR PRICE
|--------------------------------------------------------------------------
*/

    $stmt = $conn->prepare("
    SELECT price 
    FROM tours 
    WHERE id=?
");

    $stmt->bind_param("i", $current_tour_id);
    $stmt->execute();

    $currentTour = $stmt->get_result()->fetch_assoc();

    $currentPrice = $currentTour['price'] ?? 0;



    /*
|--------------------------------------------------------------------------
| LOGGED USER REFERENCES
|--------------------------------------------------------------------------
*/

    $refPrice = 0;
    $refDuration = "";

    $timePrice = 0;
    $timeDuration = "";

    $bookPrice = 0;
    $bookDuration = "";

    $reviewPrice = 0;
    $reviewDuration = "";


    if ($user_id) {


        /* MOST VIEWED */

        $stmt = $conn->prepare("
SELECT t.*
FROM tours t
JOIN user_activity ua 
ON t.id=ua.package_id

WHERE ua.user_id=?

ORDER BY ua.view_count DESC

LIMIT 1
");

        $stmt->bind_param("i", $user_id);
        $stmt->execute();

        $viewTour = $stmt->get_result()->fetch_assoc();


        $refPrice = $viewTour['price'] ?? 0;
        $refDuration = $viewTour['duration'] ?? "";



        /* MOST TIME SPENT */

        $stmt = $conn->prepare("
SELECT t.*
FROM tours t
JOIN user_activity ua
ON t.id=ua.package_id

WHERE ua.user_id=?

ORDER BY ua.time_spent DESC

LIMIT 1
");

        $stmt->bind_param("i", $user_id);
        $stmt->execute();

        $timeTour = $stmt->get_result()->fetch_assoc();


        $timePrice = $timeTour['price'] ?? 0;
        $timeDuration = $timeTour['duration'] ?? "";




        /* MOST BOOKED */

        $stmt = $conn->prepare("
SELECT t.*
FROM tours t
JOIN package_bookings pb
ON t.id=pb.package_id

WHERE pb.user_id=?

GROUP BY t.id

ORDER BY COUNT(*) DESC

LIMIT 1
");

        $stmt->bind_param("i", $user_id);
        $stmt->execute();


        $bookTour = $stmt->get_result()->fetch_assoc();


        $bookPrice = $bookTour['price'] ?? 0;
        $bookDuration = $bookTour['duration'] ?? "";




        /* MOST RATED BY USER */

        $stmt = $conn->prepare("
SELECT t.*

FROM tours t

JOIN trip_reviews tr

ON t.id=tr.trip_id


WHERE tr.user_id=?

GROUP BY t.id

ORDER BY AVG(tr.rating) DESC

LIMIT 1
");


        $stmt->bind_param("i", $user_id);
        $stmt->execute();


        $reviewTour = $stmt->get_result()->fetch_assoc();



        $reviewPrice = $reviewTour['price'] ?? 0;
        $reviewDuration = $reviewTour['duration'] ?? "";
    }



    /*
|--------------------------------------------------------------------------
| FINAL RECOMMENDATION QUERY
|--------------------------------------------------------------------------
*/


    $stmt = $conn->prepare("

SELECT

t.*,


(

/* VIEW HISTORY */

CASE
WHEN t.price BETWEEN ?-5000 AND ?+5000
THEN 10
ELSE 0
END


+

CASE
WHEN t.duration=?
THEN 8
ELSE 0
END



+

/* TIME SPENT */

CASE
WHEN t.price BETWEEN ?-5000 AND ?+5000
THEN 12
ELSE 0
END


+

CASE
WHEN t.duration=?
THEN 10
ELSE 0
END



+

/* BOOKING HISTORY */

CASE
WHEN t.price BETWEEN ?-5000 AND ?+5000
THEN 15
ELSE 0
END


+

CASE
WHEN t.duration=?
THEN 12
ELSE 0
END



+

/* REVIEW PREFERENCE */

CASE
WHEN t.price BETWEEN ?-5000 AND ?+5000
THEN 10
ELSE 0
END


+

CASE
WHEN t.duration=?
THEN 8
ELSE 0
END



+

/* COLLAB FILTER */

CASE

WHEN t.id IN(

SELECT pb2.package_id

FROM package_bookings pb1

JOIN package_bookings pb2

ON pb1.user_id=pb2.user_id


WHERE pb1.package_id IN(

SELECT package_id

FROM package_bookings

WHERE user_id=?

)

)

THEN 8

ELSE 0

END



+

/* PRICE */

CASE

WHEN t.price BETWEEN ?-5000 AND ?+5000

THEN 5

ELSE 0

END



+

/* BOOKINGS */

(COUNT(pb.id)*2)



+

/* RATING */

(

SELECT IFNULL(AVG(tr.rating),0)*8

FROM trip_reviews tr

WHERE tr.trip_id=t.id

AND tr.status=1

)



+

/* REVIEW COUNT MAX 20 */

(

SELECT LEAST(COUNT(tr.id)*2,20)

FROM trip_reviews tr

WHERE tr.trip_id=t.id

AND tr.status=1

)



+

/* POPULAR */

CASE

WHEN t.is_popular=1

THEN 10

ELSE 0

END



+

/* RECENT */

CASE

WHEN t.created_at >= NOW()-INTERVAL 7 DAY

THEN 2

ELSE 0

END


+

1


) AS score



FROM tours t


LEFT JOIN package_bookings pb

ON t.id=pb.package_id



WHERE t.id!=?

AND t.status=1



GROUP BY t.id


ORDER BY score DESC


LIMIT ?

");



    $stmt->bind_param(
        "iisiisiisiiisiiii",

        $refPrice,
        $refPrice,
        $refDuration,

        $timePrice,
        $timePrice,
        $timeDuration,

        $bookPrice,
        $bookPrice,
        $bookDuration,

        $reviewPrice,
        $reviewPrice,
        $reviewDuration,

        $current_tour_id,

        $currentPrice,
        $currentPrice,

        $current_tour_id,

        $limit
    );



    $stmt->execute();


    return $stmt->get_result();
}
