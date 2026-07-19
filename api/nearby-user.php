<?php

/**
 * AJAX endpoint: GET api/nearby-user.php?lat=..&lng=..&exclude=..
 *
 * Returns JSON list of tours near the user's live GPS location (Haversine),
 * used by assets/js/nearby-packages.js after requesting browser geolocation.
 * Kept separate from tour-details.php so it can be polled independently
 * without re-rendering the whole page.
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/recommendation.php'; // for the ArrayResult class
require_once __DIR__ . '/nearby.php';

$lat     = isset($_GET['lat']) ? (float)$_GET['lat'] : null;
$lng     = isset($_GET['lng']) ? (float)$_GET['lng'] : null;
$exclude = isset($_GET['exclude']) ? (int)$_GET['exclude'] : 0;
$radius  = isset($_GET['radius']) ? (float)$_GET['radius'] : 300;

if ($lat === null || $lng === null || $lat < -90 || $lat > 90 || $lng < -180 || $lng > 180) {
    http_response_code(400);
    echo json_encode(['error' => 'Valid lat & lng query params are required.']);
    exit;
}

$nearby = getNearbyToursForUser($conn, $lat, $lng, $exclude, $radius, 8);

$out = [];
while ($row = $nearby->fetch_assoc()) {
    $out[] = [
        'id'            => (int)$row['id'],
        'title'         => $row['title'],
        'duration'      => $row['duration'],
        'price'         => $row['price'],
        'price_usd'     => $row['price_usd'],
        'banner_image'  => $row['banner_image'],
        'location_name' => $row['location_name'],
        'distance_km'   => $row['distance_km'],
    ];
}

echo json_encode(['tours' => $out]);
