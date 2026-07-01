<?php 
$pageTitle = "Flight Details";
include 'includes/header.php'; ?>
<div class="header-wrapper">
  <?php include 'includes/topbar.php'; ?>
  <?php include 'includes/navbar.php'; ?>
</div>

<?php
include 'config/db.php';

if (!isset($_GET['id'])) {
  header("Location: index.php");
  exit;
}

$id = intval($_GET['id']);
$query = mysqli_query($conn, "SELECT * FROM flights WHERE id = $id");
$flight = mysqli_fetch_assoc($query);

if (!$flight) {
  echo "<p>Flight not found.</p>";
  exit;
}
?>
<section class="page-banner">
  <div class="overlay">
    <h1>International Flight Booking</h1>
    <div class="flightdestination">
      <strong>From:</strong> <?php echo $flight['from_city']; ?>
      <strong>To:</strong> <?php echo $flight['to_city']; ?>
    </div>
  </div>
</section>

<section class="flight-details-section">

  <div class="container flight-details-grid">

    <!-- LEFT IMAGE -->
    <div class="flight-image">



      <img src="admin/uploads/images/flights/<?php echo $flight['image']; ?>" alt="">
    </div>

    <!-- RIGHT CONTENT -->
    <div class="flight-details">

      <div class="badge-container">
        <?php if ($flight['is_group_fare'] == 1): ?>
          <div class="group-fare-badge-details">
            <i class="fa-solid fa-users"></i> Group Fare
          </div>
        <?php endif; ?>
      </div>

      <p class="flight-desc">
        <?php echo nl2br($flight['description']); ?>
      </p>

      <div class="fp-btn">
        <a href="contact" class="btn-primary-fp">
          Contact for Booking
        </a>
      </div>
    </div>

  </div>
</section>
<?php include 'includes/footer.php'; ?>