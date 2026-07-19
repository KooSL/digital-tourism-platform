<?php
/**
 * ============================================================================
 *  SMART HYBRID RECOMMENDATION ENGINE  (v2)
 * ============================================================================
 *
 *  Replaces the old single hard-coded SQL scoring query with a maintainable,
 *  explainable, weighted-hybrid model made of 5 signals:
 *
 *    1. CONTENT-BASED SIMILARITY   - price closeness (smooth decay, not a
 *                                    hard ±5000 cutoff) + duration match +
 *                                    same tour "type" match, compared against
 *                                    a *blended* user-taste profile built
 *                                    from views, time-spent, bookings and
 *                                    reviews (not just "the most recent one").
 *    2. COLLABORATIVE FILTERING    - "users who booked what you booked also
 *                                    booked this" (item-based CF via co-
 *                                    booking counts).
 *    3. POPULARITY                 - log-dampened booking + click counts so
 *                                    one viral tour can't drown everything.
 *    4. BAYESIAN RATING            - IMDB-style Bayesian average instead of
 *                                    raw AVG(rating), so a tour with one 5*
 *                                    review can't outrank a tour with fifty
 *                                    4.8* reviews.
 *    5. FRESHNESS                  - small recency boost for newly added
 *                                    tours (cold-start help).
 *
 *  All signals are normalised to 0..1 before being combined, so the weights
 *  below are the *actual* relative importance of each signal (easy to tune).
 * ============================================================================
 */

/* ---------------------------------------------------------------------------
 |  TUNABLE WEIGHTS  (must sum to 1.0 - not enforced, just a convention)
 |-------------------------------------------------------------------------*/
const REC_WEIGHTS = [
    'content'       => 0.30,   // price/duration/type match to user taste
    'collaborative' => 0.20,   // co-booking pattern with similar users
    'popularity'    => 0.15,   // bookings + clicks
    'rating'        => 0.25,   // bayesian rating
    'freshness'     => 0.10,   // newly added tours
];

/* Bayesian prior: how many "average" votes a brand-new tour is assumed to
 * start with. Higher = new tours need more reviews before rating matters. */
const BAYESIAN_MIN_VOTES = 5;

/**
 * Public entry point - kept the same name/signature so tour-details.php
 * does not need to change how it calls this file.
 *
 * @return mysqli_result-like array-based result via a tiny wrapper so the
 *         calling code's `while ($row = $recommended->fetch_assoc())`
 *         keeps working unchanged.
 */
function getRecommendations($conn, $current_tour_id, $limit = 5)
{
    $user_id = $_SESSION['user_id'] ?? null;

    $currentTour = fetchCurrentTour($conn, $current_tour_id);
    if (!$currentTour) {
        return new ArrayResult([]);
    }

    $tasteProfile = buildUserTasteProfile($conn, $user_id, $currentTour);
    $coBooked     = $user_id ? getCollaborativeCandidates($conn, $user_id) : [];
    $globalStats  = getGlobalRatingStats($conn);
    $candidates   = fetchCandidateTours($conn, $current_tour_id);

    $scored = [];
    foreach ($candidates as $tour) {
        $scored[] = scoreTour($tour, $tasteProfile, $coBooked, $globalStats, $currentTour);
    }

    usort($scored, fn($a, $b) => $b['_score'] <=> $a['_score']);

    return new ArrayResult(array_slice($scored, 0, $limit));
}

/* ---------------------------------------------------------------------------
 |  1. CURRENT TOUR
 |-------------------------------------------------------------------------*/
function fetchCurrentTour($conn, $id)
{
    $stmt = $conn->prepare("SELECT id, price, duration, type FROM tours WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

/* ---------------------------------------------------------------------------
 |  2. USER TASTE PROFILE
 |     Instead of picking a single "most viewed" tour, we blend price /
 |     duration / type preference across every signal we have, weighted by
 |     how strong that signal is (view count, time spent, bookings, ratings).
 |     This is far more representative of what the user actually likes.
 |-------------------------------------------------------------------------*/
function buildUserTasteProfile($conn, $user_id, $currentTour)
{
    // Always seed the profile with the tour being viewed right now,
    // so logged-out users / users with no history still get relevant results.
    $profile = [
        'price'      => (float)$currentTour['price'],
        'durations'  => [$currentTour['duration'] => 1.0],
        'types'      => [$currentTour['type'] => 1.0],
    ];

    if (!$user_id) {
        return $profile;
    }

    $weightedPrices = [(float)$currentTour['price'] => 1.0];

    // --- Views (weighted by view_count) & Time spent (weighted by seconds)
    $stmt = $conn->prepare("
        SELECT t.price, t.duration, t.type, ua.view_count, ua.time_spent
        FROM tours t
        JOIN user_activity ua ON t.id = ua.package_id
        WHERE ua.user_id = ?
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $activity = $stmt->get_result();

    while ($row = $activity->fetch_assoc()) {
        $viewWeight = min((int)$row['view_count'], 20) / 20;      // cap influence
        $timeWeight = min((int)$row['time_spent'], 600) / 600;    // cap at 10 min
        $w = ($viewWeight * 1.5) + ($timeWeight * 2.0);           // time spent counts more than raw views
        if ($w <= 0) continue;

        $weightedPrices[(float)$row['price']] = ($weightedPrices[(float)$row['price']] ?? 0) + $w;
        $profile['durations'][$row['duration']] = ($profile['durations'][$row['duration']] ?? 0) + $w;
        $profile['types'][$row['type']] = ($profile['types'][$row['type']] ?? 0) + $w;
    }

    // --- Bookings (strongest signal - user paid real money)
    $stmt = $conn->prepare("
        SELECT t.price, t.duration, t.type, COUNT(*) AS cnt
        FROM tours t
        JOIN package_bookings pb ON t.id = pb.package_id
        WHERE pb.user_id = ?
        GROUP BY t.id
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $bookings = $stmt->get_result();

    while ($row = $bookings->fetch_assoc()) {
        $w = 4.0 * (int)$row['cnt']; // bookings weigh heavily
        $weightedPrices[(float)$row['price']] = ($weightedPrices[(float)$row['price']] ?? 0) + $w;
        $profile['durations'][$row['duration']] = ($profile['durations'][$row['duration']] ?? 0) + $w;
        $profile['types'][$row['type']] = ($profile['types'][$row['type']] ?? 0) + $w;
    }

    // --- Reviews left by the user (weighted by rating - a 5* review says more
    //     about taste than a 1* review, which is really a complaint signal)
    $stmt = $conn->prepare("
        SELECT t.price, t.duration, t.type, tr.rating
        FROM tours t
        JOIN trip_reviews tr ON t.id = tr.trip_id
        WHERE tr.user_id = ?
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $reviews = $stmt->get_result();

    while ($row = $reviews->fetch_assoc()) {
        $w = max((int)$row['rating'] - 2, 0) * 1.5; // only 3*+ reviews signal positive taste
        if ($w <= 0) continue;
        $weightedPrices[(float)$row['price']] = ($weightedPrices[(float)$row['price']] ?? 0) + $w;
        $profile['durations'][$row['duration']] = ($profile['durations'][$row['duration']] ?? 0) + $w;
        $profile['types'][$row['type']] = ($profile['types'][$row['type']] ?? 0) + $w;
    }

    // Collapse weighted price points into a single weighted-average target price
    $sumW = array_sum($weightedPrices);
    $sumWP = 0;
    foreach ($weightedPrices as $price => $w) {
        $sumWP += $price * $w;
    }
    $profile['price'] = $sumW > 0 ? $sumWP / $sumW : (float)$currentTour['price'];

    return $profile;
}

/* ---------------------------------------------------------------------------
 |  3. COLLABORATIVE FILTERING CANDIDATES
 |     "Users who booked what this user booked also booked these tours."
 |     Returns [tour_id => co_booking_count]
 |-------------------------------------------------------------------------*/
function getCollaborativeCandidates($conn, $user_id)
{
    $stmt = $conn->prepare("
        SELECT pb2.package_id, COUNT(*) AS cnt
        FROM package_bookings pb1
        JOIN package_bookings pb2
             ON pb1.user_id = pb2.user_id AND pb2.package_id != pb1.package_id
        WHERE pb1.package_id IN (
            SELECT package_id FROM package_bookings WHERE user_id = ?
        )
        GROUP BY pb2.package_id
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $map = [];
    while ($row = $result->fetch_assoc()) {
        $map[(int)$row['package_id']] = (int)$row['cnt'];
    }
    return $map;
}

/* ---------------------------------------------------------------------------
 |  4. GLOBAL RATING STATS (needed for the Bayesian formula)
 |-------------------------------------------------------------------------*/
function getGlobalRatingStats($conn)
{
    $res = mysqli_query($conn, "
        SELECT AVG(rating) AS global_avg
        FROM trip_reviews
        WHERE status = 1
    ");
    $row = mysqli_fetch_assoc($res);
    return [
        'global_avg' => $row['global_avg'] !== null ? (float)$row['global_avg'] : 3.5,
    ];
}

/**
 * Bayesian (weighted) rating - same formula IMDB popularised:
 *
 *   WR = (v / (v + m)) * R  +  (m / (v + m)) * C
 *
 *   R = tour's own average rating
 *   v = number of reviews the tour has
 *   m = minimum votes required before a tour's own rating is trusted
 *   C = the average rating across the whole platform
 *
 * This stops a single 5-star review from outranking a tour with dozens of
 * consistently strong (but not perfect) reviews.
 */
function bayesianRating($R, $v, $m, $C)
{
    if ($v <= 0) return $C; // no reviews yet -> assume platform average
    return (($v / ($v + $m)) * $R) + (($m / ($v + $m)) * $C);
}

/* ---------------------------------------------------------------------------
 |  5. CANDIDATE TOURS  (with pre-aggregated bookings / clicks / reviews)
 |-------------------------------------------------------------------------*/
function fetchCandidateTours($conn, $exclude_id)
{
    $sql = "
        SELECT
            t.*,
            COALESCE(b.booking_count, 0)  AS booking_count,
            COALESCE(c.click_count, 0)    AS click_count,
            COALESCE(r.review_count, 0)   AS review_count,
            COALESCE(r.avg_rating, 0)     AS avg_rating
        FROM tours t
        LEFT JOIN (
            SELECT package_id, COUNT(*) AS booking_count
            FROM package_bookings
            GROUP BY package_id
        ) b ON b.package_id = t.id
        LEFT JOIN (
            SELECT package_id, SUM(total_clicks) AS click_count
            FROM recmnd_clicks
            GROUP BY package_id
        ) c ON c.package_id = t.id
        LEFT JOIN (
            SELECT trip_id, COUNT(*) AS review_count, AVG(rating) AS avg_rating
            FROM trip_reviews
            WHERE status = 1
            GROUP BY trip_id
        ) r ON r.trip_id = t.id
        WHERE t.id != ? AND t.status = 1
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $exclude_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $rows = [];
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
    return $rows;
}

/* ---------------------------------------------------------------------------
 |  6. SCORING
 |-------------------------------------------------------------------------*/
function scoreTour($tour, $tasteProfile, $coBooked, $globalStats, $currentTour)
{
    $price = (float)$tour['price'];

    /* ---- Content-based similarity (0..1) ---------------------------- */
    // Smooth exponential decay instead of a hard ±5000 cutoff, so a tour
    // that's 5001 away isn't treated as "completely irrelevant".
    $priceDiff  = abs($price - $tasteProfile['price']);
    $priceScore = exp(-$priceDiff / 6000); // ~0.37 similarity at 6000 diff, ~0.03 at 20000

    $durationWeights = $tasteProfile['durations'];
    $durationTotal    = array_sum($durationWeights) ?: 1;
    $durationScore    = ($durationWeights[$tour['duration']] ?? 0) / $durationTotal;

    $typeWeights = $tasteProfile['types'];
    $typeTotal    = array_sum($typeWeights) ?: 1;
    $typeScore    = ($typeWeights[$tour['type']] ?? 0) / $typeTotal;

    $contentScore = (0.5 * $priceScore) + (0.3 * $durationScore) + (0.2 * $typeScore);

    /* ---- Collaborative filtering (0..1) ------------------------------ */
    $maxCoBooked = $coBooked ? max($coBooked) : 0;
    $collabScore = $maxCoBooked > 0
        ? ($coBooked[(int)$tour['id']] ?? 0) / $maxCoBooked
        : 0;

    /* ---- Popularity (0..1, log-dampened) ------------------------------ */
    $popularityRaw   = log(1 + (int)$tour['booking_count'] * 3 + (int)$tour['click_count']);
    $popularityScore = 1 - exp(-$popularityRaw / 4); // squashes into 0..1

    /* ---- Bayesian rating (0..1) --------------------------------------- */
    $bayesian = bayesianRating(
        (float)$tour['avg_rating'],
        (int)$tour['review_count'],
        BAYESIAN_MIN_VOTES,
        $globalStats['global_avg']
    );
    $ratingScore = $bayesian / 5;

    /* ---- Freshness (0..1) ---------------------------------------------- */
    $ageDays = (strtotime('now') - strtotime($tour['created_at'])) / 86400;
    $freshnessScore = $ageDays <= 30 ? max(0, 1 - ($ageDays / 30)) : 0;

    /* ---- Small manual boost for admin-flagged popular tours ------------ */
    $manualBoost = ((int)$tour['is_popular'] === 1) ? 0.05 : 0;

    $finalScore =
        (REC_WEIGHTS['content']       * $contentScore) +
        (REC_WEIGHTS['collaborative'] * $collabScore) +
        (REC_WEIGHTS['popularity']    * $popularityScore) +
        (REC_WEIGHTS['rating']        * $ratingScore) +
        (REC_WEIGHTS['freshness']     * $freshnessScore) +
        $manualBoost;

    $tour['_score']            = $finalScore;
    $tour['bayesian_rating']   = round($bayesian, 1);
    $tour['review_count']      = (int)$tour['review_count'];

    return $tour;
}

/* ---------------------------------------------------------------------------
 |  Tiny drop-in replacement for a mysqli_result so tour-details.php's
 |  `while ($row = $recommended->fetch_assoc())` keeps working with zero
 |  changes to the calling page.
 |-------------------------------------------------------------------------*/
class ArrayResult
{
    private array $rows;
    private int $pointer = 0;

    public function __construct(array $rows)
    {
        $this->rows = array_values($rows);
    }

    public function fetch_assoc()
    {
        if ($this->pointer >= count($this->rows)) {
            return null;
        }
        return $this->rows[$this->pointer++];
    }

    public function count(): int
    {
        return count($this->rows);
    }
}
