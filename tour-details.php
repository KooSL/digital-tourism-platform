<?php include 'includes/header.php'; ?>

<?php

// Get tour ID from URL
$id = intval($_GET['id'] ?? 0);

if (empty($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

include 'config/db.php';
include 'includes/mailer.php';
include 'api/recommendation.php';


// Fetch tour details using prepared statement
$stmt = mysqli_prepare($conn, "SELECT * FROM tours WHERE id=? AND status=1");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$tour = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

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

  $tour_name = mysqli_real_escape_string($conn, $_POST['tour_name']);
  $name      = mysqli_real_escape_string($conn, $_POST['name']);
  $email     = mysqli_real_escape_string($conn, $_POST['email']);
  $phone     = mysqli_real_escape_string($conn, $_POST['phone']);
  $message   = mysqli_real_escape_string($conn, $_POST['message']);

  // Insert inquiry using prepared statement
  $stmt = mysqli_prepare($conn, "INSERT INTO inquiries (tour_name, name, email, phone, message) VALUES (?, ?, ?, ?, ?)");
  mysqli_stmt_bind_param($stmt, "sssss", $tour_name, $name, $email, $phone, $message);
  mysqli_stmt_execute($stmt);
  $success = mysqli_stmt_affected_rows($stmt) > 0;
  mysqli_stmt_close($stmt);

  if ($success) {
    require_once 'includes/fcm.php';
    sendFCMToAdmins(
      $conn,
      "New Tour Inquiry",
      "New inquiry received for " . $tour_name
    );

    $subject = "New Inquiry from $name for $tour_name";
    $body = "
        <h3>New Inquiry Received</h3>
        <p><strong>Tour:</strong> $tour_name</p>
        <p><strong>Name:</strong> $name</p>
        <p><strong>Email:</strong> $email</p>
        <p><strong>Phone:</strong> $phone</p>
        <p><strong>Message:</strong> $message</p>
    ";
    sendAdminMail($subject, $body);

    header("Location: tour-details?id=$id&success=sent");
    exit;
  } else {
    header("Location: tour-details?id=$id&error=failed");
    exit;
  }
}

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

// if (isset($_SESSION['user_id'])) {
//   $uid = $_SESSION['user_id'];

//   $stmt = $conn->prepare("
//     INSERT INTO user_activity (user_id, package_id, action, time_spent)
//     VALUES (?, ?, 'view', 0)
//     ON DUPLICATE KEY UPDATE action = 'view'
//   ");
//   $stmt->bind_param("ii", $uid, $id);
//   $stmt->execute();
// }

if (isset($_GET['rec']) && isset($_SESSION['user_id'])) {

  $uid = $_SESSION['user_id'];
  $pid = $tour['id'];

  mysqli_query($conn, "
        INSERT INTO recmnd_clicks
        (user_id, package_id, total_clicks)
        VALUES
        ($uid,$pid, 1)
        ON DUPLICATE KEY UPDATE total_clicks = total_clicks + 1;
    ");
}

$recommended = getRecommendations($conn, $tour['id']);

?>

<div class="header-wrapper">
  <?php include 'includes/topbar.php'; ?>
  <?php include 'includes/navbar.php'; ?>
</div>

<?php
// Sanitize and validate tour ID
if ($id <= 0) {
  echo "<p class='pageError invalidId'>Invalid tour ID!</p>";
  exit;
}

if (!$tour) {
  echo "<p class='pageError tournotfound'>Tour not found!</p>";
  exit;
}
?>

<!-- BANNER -->
<section class="tour-banner"
  style="background-image: url('admin/uploads/images/tours/<?= $tour['banner_image'] ?>');">

  <div class="overlay">
    <div class="container">

      <?php if (isset($_GET['success'])): ?>
        <div class="success-box" id="successBox">
          <strong>Success!</strong>
          <?php
          if ($_GET['success'] === 'sent') echo "Your inquiry has been sent successfully. We’ll contact you soon.";
          if ($_GET['success'] === 'booked') echo "Your package has been booked successfully. We’ll contact you soon.";
          if ($_GET['success'] === 'signin') echo "Sign in successful! Welcome, " . (isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'User') . ".";
          ?>
        </div>
      <?php endif; ?>

      <?php if (isset($_GET['error'])): ?>
        <div class="error-box package" id="errorBox">
          <strong>Error!</strong>
          <?php
          if ($_GET['error'] === 'failed') echo "Inquiry failed to send. Please try again.";
          if ($_GET['error'] === 'booking_failed') echo "Booking failed. Please try again.";
          ?>
        </div>
      <?php endif; ?>

      <h1><?= $tour['title'] ?></h1>
      <p><?= $tour['duration'] ?></p>

      <div class="banner-bottom-info">

        <div id="weatherBox">
          <p><i class="fa-solid fa-temperature-full"></i>Temperature: Loading weather...</p>
        </div>

        <div class="popular-badge-detail-box">
          <?php if ($tour['is_popular'] == 1): ?>
            <span class="popular-badge-detail"><i class="fa-solid fa-fire"></i> Popular</span>
          <?php endif; ?>
        </div>

      </div>

    </div>
  </div>
</section>


<!-- MAIN CONTENT -->
<section class="container tour-layout">

  <div class="tour-content">

    <h2 class="trip-overview">Trip Overview</h2>
    <p>
      <?= $tour['overview'] ?>
    </p>

    <h2>Trip Highlights</h2>
    <ul class="tour-highlights">
      <?php renderList($tour['highlights']); ?>
    </ul>

    <h2>Detailed Itinerary</h2>

    <div class="itinerary-list">
      <?php
      $itinerary = mysqli_query(
        $conn,
        "SELECT * FROM tour_itineraries
      WHERE tour_id = $id
      ORDER BY day_number ASC"
      );

      while ($day = mysqli_fetch_assoc($itinerary)) {
      ?>
        <div class="itinerary-day">
          <h3>Day <?= $day['day_number']; ?>: <?= htmlspecialchars($day['title']); ?></h3>
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

      <!-- ORIGINAL PRICE (optional for discount) -->
      <?php if (!empty($tour['old_price'])): ?>
        <p class="old-price">NPR <?= $tour['old_price'] ?></p>
      <?php endif; ?>

      <!-- CURRENT PRICE -->
      <p class="current-price">
        NPR <?= $tour['price'] ?>
        <span>| USD $<?= $tour['price_usd'] ?> PP</span>
      </p>

      <!-- DISCOUNT BADGE -->
      <?php if (!empty($tour['old_price'])):
        $discount = round((($tour['old_price'] - $tour['price']) / $tour['old_price']) * 100);
      ?>
        <span class="discount-badge">
          <?= $discount ?>% OFF
        </span>
      <?php endif; ?>

      <div class="group-discount">
        <p><strong>Group Discounts:</strong></p>
        <ul>
          <li>5+ persons - <span>10% OFF</span></li>
          <li>10+ persons - <span>20% OFF</span></li>
        </ul>
      </div>

      <!-- EXTRA INFO -->
      <ul class="price-features">
        <li><i class="fa fa-check"></i> Best price guarantee</li>
        <li><i class="fa fa-check"></i> No hidden charges</li>
        <li><i class="fa fa-check"></i> Instant confirmation</li>
      </ul>

      <!-- NOTE -->
      <p class="note">
        * Final price may vary based on taxes and travelers.
      </p>

      <a href="booking?id=<?= $tour['id'] ?>" class="download-btn booking">
        Book Now
      </a>

    </div>


    <div class="map-box sidebar-map">
      <h3>Trip Location</h3>

      <div id="map" style="height:300px;"></div>
    </div>


    <!-- <div class="download-box sidebar-download">
      <h3>Book Package</h3>
      <p>Secure your spot on this amazing trip!</p>

      <a href="booking?id=<?= $tour['id'] ?>" class="download-btn">
        Book Now
      </a>
      <a href="signin?redirect=<?= urlencode($_SERVER['REQUEST_URI']) ?>" class="download-btn">
        Book Now
      </a>
    </div> -->

    <div class="inquiry-box sidebar-inquiry">
      <h3>Trip Inquiry</h3>

      <form method="POST" id="userForm" novalidate>

        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

        <input type="hidden" name="tour_name" value="<?= $tour['title']; ?>">

        <div class="form-group">
          <input type="text" name="name" id="name" placeholder="Full Name">
          <small class="error"></small>
        </div>

        <div class="form-group">
          <input type="email" name="email" id="email" placeholder="Email (Optional)">
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

</section>

<section class="container recommend-section">

  <h3>Recommended for You</h3>

  <div class="recommend-grid">

    <?php while ($row = $recommended->fetch_assoc()): ?>

      <div class="recommend-card">
        <img src="admin/uploads/images/tours/<?= $row['banner_image'] ?>">

        <h4><?= $row['title'] ?></h4>

        <div class="recommend-info">
          <p><i class="fa-solid fa-clock"></i> <?= $row['duration'] ?></p>
        </div>

        <p class="current-price recommend-price">
          NPR <?= $row['price'] ?>
          <span>| USD $<?= $row['price_usd'] ?> PP</span>
        </p>

        <!-- <a href="tour-details?id=<?= $row['id'] ?>">View</a> -->
        <a href="tour-details?id=<?= $row['id'] ?>&rec=1">View</a>
      </div>

    <?php endwhile; ?>

  </div>

</section>

<script src="assets/js/inq-cnt-validation.js"></script>
<script src="assets/js/success-errorBox.js"></script>
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<script>
  const currentTripId = <?= $current_tour_id ?>;
  const latitude = <?= $latitude ?>;
  const longitude = <?= $longitude ?>;
  const locationName = "<?= addslashes($location_name) ?>";
</script>

<script src="api/tripMap.js"></script>
<script src="api/weather.js"></script>
<script src="assets/js/track-time.js"></script>

<?php include 'includes/footer.php'; ?>