<?php
/**
 * ============================================================================
 *  NEARBY PACKAGES  (Haversine Distance)
 * ============================================================================
 *
 *  Purely geographic - completely separate from the hybrid recommendation
 *  engine (api/recommendation.php). This answers "what other packages are
 *  physically close to the one I'm looking at / close to me?" rather than
 *  "what would I probably like?".
 *
 *  The Haversine formula computes great-circle distance between two
 *  lat/lng points on a sphere (Earth):
 *
 *      a = sin²(Δlat/2) + cos(lat1)·cos(lat2)·sin²(Δlng/2)
 *      c = 2 · atan2(√a, √(1−a))
 *      d = R · c            (R = Earth's radius, 6371 km)
 *
 *  We do the math directly in SQL (MySQL has all the trig functions we
 *  need) so the database only returns rows that are actually within range,
 *  already sorted by distance - no need to pull every tour into PHP first.
 * ============================================================================
 */

const EARTH_RADIUS_KM = 6371;

/**
 * Get tours near a given lat/lng point.
 *
 * @param mysqli $conn
 * @param float  $lat        latitude of the reference point
 * @param float  $lng        longitude of the reference point
 * @param int    $exclude_id tour id to exclude (usually the tour being viewed)
 * @param float  $radiusKm   max distance to consider "nearby"
 * @param int    $limit      max results
 * @return ArrayResult       same fetch_assoc()-style wrapper used by
 *                            getRecommendations(), so calling code doesn't
 *                            need to know/care which engine produced it.
 */
function getNearbyTours($conn, $lat, $lng, $exclude_id, $radiusKm = 300, $limit = 6)
{
    // No coordinates on the reference tour -> nothing to compare against.
    if ($lat === null || $lng === null || $lat === '' || $lng === '') {
        return new ArrayResult([]);
    }

    $sql = "
        SELECT
            t.*,
            (
                ? * ACOS(
                    LEAST(1, GREATEST(-1,
                        COS(RADIANS(?)) * COS(RADIANS(t.latitude))
                        * COS(RADIANS(t.longitude) - RADIANS(?))
                        + SIN(RADIANS(?)) * SIN(RADIANS(t.latitude))
                    ))
                )
            ) AS distance_km
        FROM tours t
        WHERE t.id != ?
          AND t.status = 1
          AND t.latitude IS NOT NULL
          AND t.longitude IS NOT NULL
        HAVING distance_km <= ?
        ORDER BY distance_km ASC
        LIMIT ?
    ";

    $earthRadius = EARTH_RADIUS_KM;

    $stmt = $conn->prepare($sql);
    // types: d(earth radius) d(lat) d(lng) d(lat) i(exclude_id) d(radius) i(limit)
    $stmt->bind_param(
        "ddddidi",
        $earthRadius,
        $lat,
        $lng,
        $lat,
        $exclude_id,
        $radiusKm,
        $limit
    );
    $stmt->execute();
    $result = $stmt->get_result();

    $rows = [];
    while ($row = $result->fetch_assoc()) {
        $row['distance_km'] = round((float)$row['distance_km'], 1);
        $rows[] = $row;
    }

    return new ArrayResult($rows);
}

/**
 * Convenience wrapper: nearby tours relative to the *current* tour's own
 * location (the common case on tour-details.php - "other packages near
 * this destination").
 */
function getNearbyToursForTour($conn, array $tour, $radiusKm = 300, $limit = 6)
{
    return getNearbyTours(
        $conn,
        $tour['latitude'] ?? null,
        $tour['longitude'] ?? null,
        $tour['id'],
        $radiusKm,
        $limit
    );
}

/**
 * Convenience wrapper: nearby tours relative to the visiting USER's live
 * GPS location (from browser geolocation - see nearby-user.php below).
 */
function getNearbyToursForUser($conn, $userLat, $userLng, $exclude_id = 0, $radiusKm = 300, $limit = 6)
{
    return getNearbyTours($conn, $userLat, $userLng, $exclude_id, $radiusKm, $limit);
}

/*
 * NOTE: This file relies on the `ArrayResult` class defined in
 * api/recommendation.php. Make sure that file is included first
 * (tour-details.php already does: recommendation.php then nearby.php).
 */