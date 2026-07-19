<?php
$pageTitle = "Tour Details";
include 'includes/header.php'; ?>

<?php

// Get tour ID from URL
$id = intval($_GET['id'] ?? 0);

if (empty($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

require_once __DIR__ . '/config/db.php';
include 'includes/mailer.php';
include 'api/recommendation.php'; // now also exposes bayesianRating(), getGlobalRatingStats()


// Fetch tour details using prepared statement
$stmt = mysqli_prepare($conn, "SELECT * FROM tours WHERE id=? AND status=1");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$tour = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

// Sanitize and validate tour ID / existence EARLY (before using $tour below)
if ($id <= 0) {
  include 'includes/topbar.php';
  include 'includes/navbar.php';
  echo "<p class='pageError invalidId'>Invalid tour ID!</p>";
  include 'includes/footer.php';
  exit;
}

if (!$tour) {
  include 'includes/topbar.php';
  include 'includes/navbar.php';
  echo "<p class='pageError tournotfound'>Tour not found!</p>";
  include 'includes/footer.php';
  exit;
}

$current_tour_id = $tour['id'];
$latitude = $tour['latitude'];
$longitude = $tour['longitude'];
$location_name = $tour['location_name'];


// Handle inquiry form submission
if (isset($_POST['send_inquiry'])) {

  if (
    !isset($_POST['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
  ) {
    die("CSRF validation failed.");
  }

  $trip_id   = (int)$_POST['trip_id'];
  $tour_name = $_POST['tour_name'] ?? '';
  $name      = trim($_POST['name'] ?? '');
  $email     = trim($_POST['email'] ?? '');
  $phone     = trim($_POST['phone'] ?? '');
  $message   = trim($_POST['message'] ?? '');

  // Insert inquiry using prepared statement
  $stmt = mysqli_prepare($conn, "INSERT INTO inquiries (trip_id, name, email, phone, message) VALUES (?, ?, ?, ?, ?)");
  mysqli_stmt_bind_param($stmt, "issss", $trip_id, $name, $email, $phone, $message);
  mysqli_stmt_execute($stmt);
  $success = mysqli_stmt_affected_rows($stmt) > 0;
  mysqli_stmt_close($stmt);

  if ($success) {
    require_once __DIR__ . '/includes/send_fcm_notification.php';
    $customerName = $name;
    sendAdminNotification(
      '📩 New Inquiry Received!',
      $customerName . ' submitted a new inquiry.',
      '/admin/inquiries.php'
    );

    $subject = "New Inquiry from " . $name . " for " . $tour_name;
    $body = "
        <h3>New Inquiry Received</h3>
        <p><strong>Tour:</strong> " . htmlspecialchars($tour_name) . "</p>
        <p><strong>Name:</strong> " . htmlspecialchars($name) . "</p>
        <p><strong>Email:</strong> " . htmlspecialchars($email) . "</p>
        <p><strong>Phone:</strong> " . htmlspecialchars($phone) . "</p>
        <p><strong>Message:</strong> " . nl2br(htmlspecialchars($message)) . "</p>
    ";
    sendAdminMail($subject, $body);

    header("Location: tour-details?id=$id&success=sent");
    exit;
  } else {
    header("Location: tour-details?id=$id&error=failed");
    exit;
  }
}

if (isset($_POST['submit_review'])) {

  if (
    !isset($_POST['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
  ) {
    die("CSRF validation failed.");
  }

  if (!isset($_SESSION['user_id'])) {
    header("Location: signin?redirect=" . urlencode($_SERVER['REQUEST_URI']));
    exit;
  }

  $trip_id  = (int)$_POST['trip_id'];
  $name     = trim($_POST['name'] ?? '');
  $rating   = max(1, min(5, (int)($_POST['rating'] ?? 0)));
  $review   = trim($_POST['review'] ?? '');
  $user_id  = $_SESSION['user_id'];

  $stmt = mysqli_prepare(
    $conn,
    "INSERT INTO trip_reviews (trip_id, user_id, name, rating, review) VALUES (?, ?, ?, ?, ?)"
  );
  mysqli_stmt_bind_param($stmt, "iisis", $trip_id, $user_id, $name, $rating, $review);
  mysqli_stmt_execute($stmt);
  mysqli_stmt_close($stmt);

  header("Location: tour-details?id=$trip_id&success=review_sent");
  exit;
}

// ---------------------------------------------------------------------------
// Bayesian rating for the hero "rating summary" badge instead of a raw AVG,
// so a tour with 1 five-star review doesn't outrank a tour with 40 solid
// 4.5-star reviews. Uses the same helper as the recommendation engine.
// ---------------------------------------------------------------------------
$stmt = mysqli_prepare($conn, "
    SELECT AVG(rating) AS avg_rating, COUNT(*) AS total_reviews
    FROM trip_reviews
    WHERE trip_id = ? AND status = 1
");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$ratingData = mysqli_stmt_get_result($stmt)->fetch_assoc();
mysqli_stmt_close($stmt);

$globalStats = getGlobalRatingStats($conn);
$bayesianDisplayRating = bayesianRating(
  (float)($ratingData['avg_rating'] ?? 0),
  (int)($ratingData['total_reviews'] ?? 0),
  BAYESIAN_MIN_VOTES,
  $globalStats['global_avg']
);

// Define helper function for rendering lists
function renderList($text)
{
  $items = preg_split("/\r\n|\n|\r/", trim($text));
  echo "<ul>";
  foreach ($items as $item) {
    if (!empty(trim($item))) {
      echo "<li>" . htmlspecialchars($item) . "</li>";
    }
  }
  echo "</ul>";
}

// Track a page view for logged-in users so the recommendation engine has
// real signal to work with (previously commented out).
if (isset($_SESSION['user_id'])) {
  $uid = $_SESSION['user_id'];

  $stmt = $conn->prepare("
    INSERT INTO user_activity (user_id, package_id, action, view_count, last_viewed_at)
    VALUES (?, ?, 'view', 1, NOW())
    ON DUPLICATE KEY UPDATE view_count = view_count + 1, last_viewed_at = NOW()
  ");
  $stmt->bind_param("ii", $uid, $id);
  $stmt->execute();
}

if (isset($_GET['rec']) && isset($_SESSION['user_id'])) {

  $uid = $_SESSION['user_id'];
  $pid = $tour['id'];

  $stmt = $conn->prepare("
        INSERT INTO recmnd_clicks (user_id, package_id, total_clicks)
        VALUES (?, ?, 1)
        ON DUPLICATE KEY UPDATE total_clicks = total_clicks + 1
    ");
  $stmt->bind_param("ii", $uid, $pid);
  $stmt->execute();
}

$recommended = getRecommendations($conn, $tour['id']);

?>

<div class="header-wrapper">
  <?php include 'includes/topbar.php'; ?>
  <?php include 'includes/navbar.php'; ?>
</div>

<!-- BANNER -->
<section class="tour-banner"
  style="background-image: url('admin/uploads/images/tours/<?= htmlspecialchars($tour['banner_image']) ?>');">

  <div class="overlay">
    <div class="container">

      <?php if (isset($_GET['success'])): ?>
        <div class="success-box" id="successBox">
          <strong>Success!</strong>
          <?php
          $successMsgs = [
            'sent'         => "Your inquiry has been sent successfully. We'll contact you soon.",
            'booked'       => "Your package has been booked successfully. We'll contact you soon.",
            'signin'       => "Sign in successful! Welcome, " . htmlspecialchars($_SESSION['user_name'] ?? 'User') . ".",
            'review_sent'  => "Thank you for your review. Your review has been sent successfully.",
          ];
          echo $successMsgs[$_GET['success']] ?? '';
          ?>
        </div>
      <?php endif; ?>

      <?php if (isset($_GET['error'])): ?>
        <div class="error-box package" id="errorBox">
          <strong>Error!</strong>
          <?php
          $errorMsgs = [
            'failed'         => "Inquiry failed to send. Please try again.",
            'booking_failed' => "Booking failed. Please try again.",
          ];
          echo $errorMsgs[$_GET['error']] ?? '';
          ?>
        </div>
      <?php endif; ?>

      <div class="banner-bottom-info">

        <div id="weatherBox">
          <p><i class="fa-solid fa-temperature-full"></i>Temperature: Loading weather...</p>
        </div>

        <div class="popular-badge-detail-box">
          <?php if ($tour['is_popular'] == 1): ?>
            <span class="popular-badge-detail"><i class="fa-solid fa-fire"></i> Popular</span>
          <?php endif; ?>
        </div>

        <div class="rating-summary" title="Bayesian-weighted rating - balances average score with number of reviews">
          <a href="#reviews"><i class="fa-solid fa-star"></i> <?= number_format($bayesianDisplayRating, 1) ?>
            (<?= (int)($ratingData['total_reviews'] ?? 0) ?> reviews)</a>
        </div>

      </div>

    </div>
  </div>
</section>

<section class="container title-content">
  <div class="title-content-box">
    <h1><?= htmlspecialchars($tour['title']) ?></h1>
    <p><?= htmlspecialchars($tour['duration']) ?></p>
  </div>
</section>

<!-- MAIN CONTENT -->
<section class="container tour-layout">

  <div class="tour-content">

    <h2 class="trip-overview">Trip Overview</h2>
    <p><?= nl2br(htmlspecialchars($tour['overview'])) ?></p>

    <h2>Trip Highlights</h2>
    <ul class="tour-highlights">
      <?php renderList($tour['highlights']); ?>
    </ul>

    <h2>Detailed Itinerary</h2>

    <div class="itinerary-list">
      <?php
      $stmt = $conn->prepare("SELECT * FROM tour_itineraries WHERE tour_id = ? ORDER BY day_number ASC");
      $stmt->bind_param("i", $id);
      $stmt->execute();
      $itinerary = $stmt->get_result();

      while ($day = $itinerary->fetch_assoc()) {
      ?>
        <div class="itinerary-day">
          <h3>Day <?= (int)$day['day_number']; ?>: <?= htmlspecialchars($day['title']); ?></h3>
          <p><?= nl2br(htmlspecialchars($day['description'])); ?></p>
        </div>
      <?php } ?>
    </div>


    <h2>Cost Includes</h2>
    <ul>
      <?php renderList($tour['includes']); ?>
    </ul>

    <h2>Cost Excludes</h2>
    <ul>
      <?php renderList($tour['excludes']); ?>
    </ul>

  </div>

  <div class="tour-sidebar">

    <div class="download-box sidebar-download">
      <h3>Trip Brochure</h3>
      <p>Download the full itinerary and trip details.</p>

      <a href="download-pdf?file=<?= urlencode($tour['pdf_file']); ?>" class="download-btn">
        <i class="fas fa-file-pdf"></i> Download PDF
      </a>
    </div>

    <div class="price-box sidebar-price">

      <h3>Trip Cost</h3>

      <?php if (!empty($tour['old_price'])): ?>
        <p class="old-price">NPR <?= htmlspecialchars($tour['old_price']) ?></p>
      <?php endif; ?>

      <p class="current-price">
        NPR <?= htmlspecialchars($tour['price']) ?>
        <span>| USD $<?= htmlspecialchars($tour['price_usd']) ?> PP</span>
      </p>

      <?php if (!empty($tour['old_price']) && (float)$tour['old_price'] > 0): ?>
        <?php $discount = round((((float)$tour['old_price'] - (float)$tour['price']) / (float)$tour['old_price']) * 100); ?>
        <span class="discount-badge"><?= $discount ?>% OFF</span>
      <?php endif; ?>

      <div class="group-discount">
        <p><strong>Group Discounts:</strong></p>
        <ul>
          <li>5+ persons - <span>10% OFF</span></li>
          <li>10+ persons - <span>20% OFF</span></li>
        </ul>
      </div>

      <ul class="price-features">
        <li><i class="fa fa-check"></i> Best price guarantee</li>
        <li><i class="fa fa-check"></i> No hidden charges</li>
        <li><i class="fa fa-check"></i> Instant confirmation</li>
      </ul>

      <p class="note">* Final price may vary based on taxes and travelers.</p>

      <a href="booking?id=<?= (int)$tour['id'] ?>" class="download-btn booking">Book Now</a>
    </div>

    <div class="map-box sidebar-map">
      <h3>Trip Location</h3>
      <div id="map" style="height:300px;"></div>
    </div>

    <div class="inquiry-box sidebar-inquiry">
      <h3>Trip Inquiry</h3>

      <form method="POST" id="userForm" novalidate>
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']); ?>">
        <input type="hidden" name="tour_name" value="<?= htmlspecialchars($tour['title']); ?>">
        <input type="hidden" name="trip_id" value="<?= (int)$tour['id']; ?>">

        <div class="form-group">
          <input type="text" name="name" id="name" placeholder="Full Name">
          <small class="error"></small>
        </div>

        <div class="form-group">
          <input type="email" name="email" id="email" placeholder="Email">
          <small class="error"></small>
        </div>

        <div class="form-group">
          <input type="text" name="phone" id="phone" placeholder="Phone">
          <small class="error"></small>
        </div>

        <div class="form-group">
          <textarea name="message" id="message" placeholder="Your Inquiry"></textarea>
          <small class="error"></small>
        </div>

        <button type="submit" name="send_inquiry">Send Inquiry</button>
      </form>
    </div>

  </div>

  <section id="reviews" class="trip-reviews">
    <div class="container">

      <div class="review-header">
        <h3>Ratings & Reviews</h3>
        <p>Share your experience and help other travelers.</p>
      </div>

      <form method="POST" class="review-form" id="reviewForm" novalidate>
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']); ?>">
        <input type="hidden" name="trip_id" value="<?= (int)$tour['id'] ?>">

        <div class="form-group">
          <input type="text" name="name" placeholder="Full name" id="reviewName"
            value="<?= htmlspecialchars($_SESSION['user_name'] ?? '') ?>">
          <small class="error"></small>
        </div>

        <div class="form-group">
          <div class="star-rating">
            <input type="radio" id="star5" name="rating" value="5">
            <label for="star5"><i class="fa-solid fa-star"></i></label>

            <input type="radio" id="star4" name="rating" value="4">
            <label for="star4"><i class="fa-solid fa-star"></i></label>

            <input type="radio" id="star3" name="rating" value="3">
            <label for="star3"><i class="fa-solid fa-star"></i></label>

            <input type="radio" id="star2" name="rating" value="2">
            <label for="star2"><i class="fa-solid fa-star"></i></label>

            <input type="radio" id="star1" name="rating" value="1">
            <label for="star1"><i class="fa-solid fa-star"></i></label>
          </div>
          <small class="error"></small>
        </div>

        <div class="form-group">
          <textarea id="review" name="review" rows="5" placeholder="Tell us about your experience..."></textarea>
          <small class="error"></small>
        </div>

        <?php if (isset($_SESSION['user_id'])) { ?>
          <button type="submit" name="submit_review">Submit Review</button>
        <?php } else { ?>
          <a href="signin?redirect=<?= urlencode($_SERVER['REQUEST_URI']) ?>" class="download-btn">Submit Review</a>
        <?php } ?>
      </form>

      <?php
      $stmt = $conn->prepare("
        SELECT * FROM trip_reviews
        WHERE trip_id = ? AND status = 1
        ORDER BY created_at DESC
      ");
      $stmt->bind_param("i", $id);
      $stmt->execute();
      $reviews = $stmt->get_result();

      while ($review = $reviews->fetch_assoc()):
      ?>
        <div class="review-card">
          <h4><?= htmlspecialchars($review['name']) ?></h4>
          <small><?= htmlspecialchars($review['created_at']) ?></small>

          <div class="stars">
            <?= str_repeat('<i class="fa-solid fa-star"></i>', (int)$review['rating']) ?>
          </div>

          <p><?= nl2br(htmlspecialchars($review['review'])) ?></p>
        </div>
      <?php endwhile; ?>

    </div>
  </section>

</section>


<section class="container recommend-section">

  <h3>Recommended for You</h3>
  <!-- <p class="recommend-subtitle">Powered by our hybrid recommendation engine - blending your browsing habits, similar travelers' bookings, and Bayesian-weighted ratings.</p> -->

  <div class="recommend-grid">

    <?php while ($row = $recommended->fetch_assoc()): ?>

      <div class="recommend-card">
        <img src="admin/uploads/images/tours/<?= htmlspecialchars($row['banner_image']) ?>" alt="<?= htmlspecialchars($row['title']) ?>">

        <h4><?= htmlspecialchars($row['title']) ?></h4>

        <div class="recommend-info">
          <p><i class="fa-solid fa-clock"></i> <?= htmlspecialchars($row['duration']) ?></p>
          <p class="recommend-rating">
            <i class="fa-solid fa-star"></i> <?= number_format($row['bayesian_rating'], 1) ?>
            <span>(<?= (int)$row['review_count'] ?>)</span>
          </p>
        </div>

        <p class="current-price recommend-price">
          NPR <?= htmlspecialchars($row['price']) ?>
          <span>| USD $<?= htmlspecialchars($row['price_usd']) ?> PP</span>
        </p>

        <a href="tour-details?id=<?= (int)$row['id'] ?>&rec=1">View</a>
      </div>

    <?php endwhile; ?>

  </div>

</section>

<script src="assets/js/inq-cnt-validation.js"></script>
<script src="assets/js/review-validation.js"></script>
<script src="assets/js/success-errorBox.js"></script>
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<script>
  const currentTripId = <?= (int)$current_tour_id ?>;
  const latitude = <?= json_encode((float)$latitude) ?>;
  const longitude = <?= json_encode((float)$longitude) ?>;
  const locationName = <?= json_encode($location_name) ?>;
</script>

<script src="api/tripMap.js"></script>
<script src="api/weather.js"></script>
<script src="assets/js/track-time.js"></script>

<?php include 'includes/footer.php'; ?>
