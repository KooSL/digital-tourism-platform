<?php
include '../config/db.php';
include 'auth.php';
?>

<?php include 'includes/header.php'; ?>
<?php include 'includes/sidebar.php'; ?>

<div class="admin-content">

  <h1>Dashboard</h1>

  <!-- STATS -->
  <div class="dashboard-stats">

    <?php
    $tourCount = $conn->query("SELECT COUNT(*) AS total FROM tours")->fetch_assoc();
    $flightCount = $conn->query("SELECT COUNT(*) AS total FROM flights")->fetch_assoc();
    $inqCount  = $conn->query("SELECT COUNT(*) AS total FROM inquiries")->fetch_assoc();
    $albumCount = $conn->query("SELECT COUNT(*) AS total FROM gallery_albums")->fetch_assoc();
    $testCount = $conn->query("SELECT COUNT(*) AS total FROM testimonials")->fetch_assoc();
    $cliCount = $conn->query("SELECT COUNT(*) AS total FROM clients")->fetch_assoc();
    $busInqCount = $conn->query("SELECT COUNT(*) AS total FROM bus_inquiries")->fetch_assoc();

    $activeTours = mysqli_fetch_assoc(
      mysqli_query($conn, "SELECT COUNT(*) as total FROM tours WHERE status=1")
    )['total'];

    $inactiveTours = mysqli_fetch_assoc(
      mysqli_query($conn, "SELECT COUNT(*) as total FROM tours WHERE status=0")
    )['total'];

    $activeFlights = mysqli_fetch_assoc(
      mysqli_query($conn, "SELECT COUNT(*) as total FROM flights WHERE status=1")
    )['total'];

    $inactiveFlights = mysqli_fetch_assoc(
      mysqli_query($conn, "SELECT COUNT(*) as total FROM flights WHERE status=0")
    )['total'];

    ?>

    <div class="stat-box">
      <p class="stat-title">Total Tours</p>
      <h3><?php echo $tourCount['total']; ?></h3>
      <p><span class="active">Active: <?php echo $activeTours; ?></span></p>
      <p><span class="inactive">Inactive: <?php echo $inactiveTours; ?></span></p>
    </div>

    <div class="stat-box">
      <p class="stat-title">Total Flights Post</p>
      <h3><?php echo $flightCount['total']; ?></h3>
      <p><span class="active">Active: <?php echo $activeFlights; ?></span></p>
      <p><span class="inactive">Inactive: <?php echo $inactiveFlights; ?></span></p>
    </div>

    <div class="stat-box">
      <p class="stat-title">Total Albums</p>
      <h3><?php echo $albumCount['total']; ?></h3>
    </div>

    <div class="stat-box">
      <p class="stat-title">Total Inquiries</p>
      <h3><?php echo $inqCount['total']; ?></h3>
      <p><span class="bus-inquiries">Bus Inquiries: <?php echo $busInqCount['total']; ?></span></p>
    </div>

    <div class="stat-box">
      <p class="stat-title">Total Testimonials</p>
      <h3><?php echo $testCount['total']; ?></h3>
    </div>
    <div class="stat-box">
      <p class="stat-title">Total Happy Clients</p>
      <h3><?php echo $cliCount['total']; ?></h3>
    </div>

    

  </div>

  <!-- RECENT INQUIRIES -->
  <div class="recent-box">
    <h2>Recent Inquiries</h2>

    <table>
      <tr>
        <th>Name</th>
        <th>Email</th>
        <th>Tour</th>
        <th>Received Date</th>
      </tr>

      <?php
      // $result = $conn->query("SELECT * FROM inquiries WHERE created_at >= NOW() - INTERVAL 24 HOUR ORDER BY id DESC LIMIT 5");
      $result = $conn->query("SELECT * FROM inquiries ORDER BY id DESC LIMIT 5") ;
      while($row = $result->fetch_assoc()):
      ?>
      <tr>
        <td><?php echo $row['name']; ?></td>
        <td><?php echo $row['email']; ?></td>
        <td><?php echo $row['tour_name']; ?></td>
        <td><?= htmlspecialchars($row['created_at']) ?></td>
      </tr>
      <?php endwhile; ?>
    </table>
  </div>

</div>

<script type="module" src="../assets/js/firebase-init.js"></script>


<?php include 'includes/footer.php'; ?>
