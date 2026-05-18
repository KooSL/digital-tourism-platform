<?php include 'includes/header.php'; ?>

<?php

if (empty($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

include 'config/db.php';
include 'includes/mailer.php';

// Get tour ID from URL
$id = intval($_GET['id'] ?? 0);


// Fetch tour details using prepared statement
$stmt = mysqli_prepare($conn, "SELECT * FROM tours WHERE id=? AND status=1");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$tour = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);



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

      <?php if (isset($_GET['success']) && $_GET['success'] === 'sent'): ?>
        <div class="success-box" id="successBox">
          <strong>Success!</strong>
          <?php
          if ($_GET['success'] === 'sent') echo "Your inquiry has been sent successfully. We’ll contact you soon.";
          ?>
        </div>
      <?php endif; ?>

      <?php if (isset($_GET['error'])): ?>
        <div class="error-box package" id="errorBox">
          <strong>Error!</strong>
          <?php
          if ($_GET['error'] === 'failed') echo "Inquiry failed to send. Please try again.";
          ?>
        </div>
      <?php endif; ?>

      <h1><?= $tour['title'] ?></h1>
      <p><?= $tour['duration'] ?></p>

      <?php if ($tour['is_popular'] == 1): ?>
        <span class="popular-badge-detail"><i class="fa-solid fa-fire"></i> Popular</span>
      <?php endif; ?>

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

    <div class="price-box sidebar-price">
      <h3>Trip Cost</h3>
      <p><strong>From:</strong> NPR <?= $tour['price'] ?> | USD $<?= $tour['price_usd'] ?></p>
    </div>

    <div class="download-box sidebar-download">
      <h3>Trip Brochure</h3>
      <p>Download the full itinerary and trip details.</p>

      <a href="download-pdf?file=<?= urlencode($tour['pdf_file']); ?>" class="download-btn">
        <i class="fas fa-file-pdf"></i> Download PDF
      </a>
    </div>

    <div class="download-box sidebar-download">
      <h3>Book Now</h3>
      <p>Secure your spot on this amazing trip!</p>

      <a href="" class="download-btn">
        Book
      </a>
    </div>

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

<script src="assets/js/inq-cnt-validation.js"></script>
<script src="assets/js/success-errorBox.js"></script>

<?php include 'includes/footer.php'; ?>