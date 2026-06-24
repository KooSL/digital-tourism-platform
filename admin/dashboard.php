<?php
include '../config/db.php';
include 'auth.php';
?>

<?php include 'includes/header.php'; ?>
<?php include 'includes/sidebar.php'; ?>

<?php

$views = mysqli_query($conn, "
    SELECT t.title, SUM(ua.view_count) AS total_views
    FROM user_activity ua
    JOIN tours t ON ua.package_id = t.id
    GROUP BY ua.package_id
    ORDER BY total_views DESC
    LIMIT 5
");

$viewslabels = [];
$viewsdata = [];

while ($row = mysqli_fetch_assoc($views)) {
  $viewslabels[] = $row['title'];
  $viewsdata[] = $row['total_views'];
}

$bookings = mysqli_query($conn, "
    SELECT DATE(created_at) as booking_date,
           COUNT(*) as total
    FROM package_bookings
    GROUP BY DATE(created_at)
    ORDER BY booking_date ASC
");

$bookingdates = [];
$bookingtotals = [];

while ($row = mysqli_fetch_assoc($bookings)) {
  $bookingdates[] = $row['booking_date'];
  $bookingtotals[] = $row['total'];
}

$types = mysqli_query($conn, "
    SELECT type, COUNT(*) as total
    FROM tours
    GROUP BY type
");

$typelabels = [];
$typedata = [];

while ($row = mysqli_fetch_assoc($types)) {
  $typelabels[] = ucfirst($row['type']);
  $typedata[] = $row['total'];
}

$totalViews = mysqli_fetch_assoc(
  mysqli_query($conn, "
        SELECT SUM(view_count) total
        FROM user_activity
    ")
)['total'];

$totalBookings = mysqli_fetch_assoc(
  mysqli_query($conn, "
        SELECT COUNT(*) total
        FROM package_bookings
    ")
)['total'];

$conversionRate =
  $totalViews > 0
  ? round(($totalBookings / $totalViews) * 100, 2)
  : 0;

$dropOffTours = mysqli_query($conn, "
    SELECT
        t.title,
        COALESCE(SUM(ua.view_count), 0) AS views,
        COUNT(DISTINCT pb.id) AS bookings

    FROM tours t

    LEFT JOIN user_activity ua
        ON ua.package_id = t.id

    LEFT JOIN package_bookings pb
        ON pb.package_id = t.id

    GROUP BY t.id

    HAVING views >= 20
       AND bookings <= 2

    ORDER BY views DESC

    LIMIT 5
");

$totalRecmndClicks = mysqli_fetch_assoc(
  mysqli_query($conn, "SELECT SUM(total_clicks) AS total FROM recmnd_clicks")
)['total'];

?>

<div class="admin-content">

  <h1>Dashboard</h1>

  <!-- STATS -->
  <div class="dashboard-stats">

    <?php
    $tourCount = $conn->query("SELECT COUNT(*) AS total FROM tours")->fetch_assoc();
    $busCount = $conn->query("SELECT COUNT(*) AS total FROM buses")->fetch_assoc();
    $flightCount = $conn->query("SELECT COUNT(*) AS total FROM flights")->fetch_assoc();
    $inqCount  = $conn->query("SELECT COUNT(*) AS total FROM inquiries")->fetch_assoc();
    $albumCount = $conn->query("SELECT COUNT(*) AS total FROM gallery_albums")->fetch_assoc();
    $testCount = $conn->query("SELECT COUNT(*) AS total FROM testimonials")->fetch_assoc();
    $cliCount = $conn->query("SELECT COUNT(*) AS total FROM clients")->fetch_assoc();
    $busInqCount = $conn->query("SELECT COUNT(*) AS total FROM bus_inquiries")->fetch_assoc();
    $pckgbookCount  = $conn->query("SELECT COUNT(*) AS total FROM package_bookings")->fetch_assoc();
    $faqCount = $conn->query("SELECT COUNT(*) AS total FROM faqs")->fetch_assoc();
    $revCount = $conn->query("SELECT COUNT(*) AS total FROM trip_reviews")->fetch_assoc();

    $activeTours = mysqli_fetch_assoc(
      mysqli_query($conn, "SELECT COUNT(*) as total FROM tours WHERE status=1")
    )['total'];

    $activeBuses = mysqli_fetch_assoc(
      mysqli_query($conn, "SELECT COUNT(*) as total FROM buses WHERE status=1")
    )['total'];

    $inactiveBuses = mysqli_fetch_assoc(
      mysqli_query($conn, "SELECT COUNT(*) as total FROM buses WHERE status=0")
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
      <p class="stat-title">Total Trips</p>
      <h3><?php echo $tourCount['total']; ?></h3>
      <p><span class="active">Active: <?php echo $activeTours; ?></span></p>
      <p><span class="inactive">Inactive: <?php echo $inactiveTours; ?></span></p>
    </div>

    <div class="stat-box">
      <p class="stat-title">Total Buses</p>
      <h3><?php echo $busCount['total']; ?></h3>
      <p><span class="active">Active: <?php echo $activeBuses; ?></span></p>
      <p><span class="inactive">Inactive: <?php echo $inactiveBuses; ?></span></p>
    </div>

    <div class="stat-box">
      <p class="stat-title">Total Flights Post</p>
      <h3><?php echo $flightCount['total']; ?></h3>
      <p><span class="active">Active: <?php echo $activeFlights; ?></span></p>
      <p><span class="inactive">Inactive: <?php echo $inactiveFlights; ?></span></p>
    </div>

    <div class="stat-box">
      <p class="stat-title">Total Package Bookings</p>
      <h3><?php echo $pckgbookCount['total']; ?></h3>
    </div>

    <div class="stat-box">
      <p class="stat-title">Total Inquiries</p>
      <h3><?php echo $inqCount['total']; ?></h3>
      <p><span class="bus-inquiries">Bus Inquiries: <?php echo $busInqCount['total']; ?></span></p>
    </div>

    <div class="stat-box">
      <p class="stat-title">Total Users</p>
      <h3><?php echo $albumCount['total']; ?></h3>
    </div>

    <div class="stat-box">
      <p class="stat-title">Total Albums</p>
      <h3><?php echo $albumCount['total']; ?></h3>
    </div>

    <div class="stat-box">
      <p class="stat-title">Total Testimonials</p>
      <h3><?php echo $testCount['total']; ?></h3>
    </div>
    <div class="stat-box">
      <p class="stat-title">Total Happy Clients</p>
      <h3><?php echo $cliCount['total']; ?></h3>
    </div>

    <div class="stat-box">
      <p class="stat-title">Total FAQs</p>
      <h3><?php echo $faqCount['total']; ?></h3>
    </div>

    <div class="stat-box">
      <p class="stat-title">Total Trip Reviews</p>
      <h3><?php echo $revCount['total']; ?></h3>
    </div>

    <div class="stat-box">
      <p class="stat-title">Conversion Rate</p>
      <h3><?php echo $conversionRate; ?>%</h3>
    </div>

    <div class="stat-box">
      <p class="stat-title">Total Recommendation Clicks</p>
      <h3><?php echo $totalRecmndClicks; ?></h3>
    </div>

  </div>

  <!-- RECENT INQUIRIES -->
  <div class="recent-box">
    <h2>Recent package Inquiries</h2>

    <table>
      <tr>
        <th>Name</th>
        <th>Email</th>
        <th>Trip</th>
        <th>Received Date</th>
      </tr>

      <?php
      // $result = $conn->query("SELECT * FROM inquiries WHERE created_at >= NOW() - INTERVAL 24 HOUR ORDER BY id DESC LIMIT 5");
      $result = $conn->query("SELECT 
        inquiries.*,
        tours.title AS tour_name

        FROM inquiries

        LEFT JOIN tours 
        ON inquiries.trip_id = tours.id

        ORDER BY inquiries.id DESC LIMIT 5");
      while ($row = $result->fetch_assoc()):
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

  <div class="charts-grid">

    <div class="chart-card large">
      <h3>Bookings Over Time</h3>
      <canvas id="bookingChart"></canvas>
    </div>

    <div class="chart-card">
      <h3>Most Viewed Trips</h3>
      <canvas id="viewsChart"></canvas>
    </div>

    <div class="chart-card">
      <h3>Domestic vs International</h3>
      <canvas id="typeChart"></canvas>
    </div>

  </div>

  <div class="analytics-section">

    <div class="tables">

      <div class="table-card">
        <h3>Top Viewed Trips</h3>
        <table>
          <tr>
            <th>Trip</th>
            <th>Views</th>
          </tr>

          <?php
          $result = $conn->query("
              SELECT t.title, SUM(ua.view_count) AS total_views
              FROM user_activity ua
              JOIN tours t ON ua.package_id = t.id
              GROUP BY ua.package_id
              ORDER BY total_views DESC
              LIMIT 5
          ");
          while ($row = $result->fetch_assoc()):
          ?>
            <tr>
              <td><?php echo $row['title']; ?></td>
              <td><?php echo $row['total_views']; ?></td>
            </tr>
          <?php endwhile; ?>
        </table>
      </div>

      <div class="table-card">
        <h3>Top Active Users</h3>
        <table>
          <tr>
            <th>User</th>
            <th>Views</th>
            <th>Bookings</th>
          </tr>

          <?php
          $result = $conn->query("
              SELECT ua.user_id, u.name,
                     SUM(ua.view_count) AS total_views,
                     (SELECT COUNT(*) FROM package_bookings pb WHERE pb.user_id = ua.user_id) AS total_bookings
              FROM user_activity ua
              JOIN users u ON ua.user_id = u.id
              GROUP BY ua.user_id
              ORDER BY total_views DESC
              LIMIT 5
          ");
          while ($row = $result->fetch_assoc()):
          ?>
            <tr>
              <td><?php echo $row['name']; ?></td>
              <td><?php echo $row['total_views']; ?></td>
              <td><?php echo $row['total_bookings']; ?></td>
            </tr>
          <?php endwhile; ?>
        </table>
      </div>

      <div class="table-card">
        <h3>Drop-off Trips</h3>

        <table>
          <tr>
            <th>Trip</th>
            <th>Views</th>
            <th>Bookings</th>
          </tr>

          <?php while ($row = mysqli_fetch_assoc($dropOffTours)): ?>
            <tr>
              <td><?= htmlspecialchars($row['title']) ?></td>
              <td><?= $row['views'] ?></td>
              <td><?= $row['bookings'] ?></td>
            </tr>
          <?php endwhile; ?>
        </table>
      </div>

      <div class="table-card">
        <h3>Recommendation Clicks</h3>
        <table>
          <tr>
            <th>User</th>
            <th>Trip</th>
            <th>Recommedation Clicks</th>
          </tr>
          <?php
          $result = $conn->query("
              SELECT * FROM recmnd_clicks rc
              JOIN users u ON rc.user_id = u.id 
              JOIN tours t ON rc.package_id = t.id
              ORDER BY total_clicks DESC LIMIT 5;
          ");
          while ($row = $result->fetch_assoc()):
          ?>
            <tr>
              <td><?php echo $row['name']; ?></td>
              <td><?php echo $row['title']; ?></td>
              <td><?php echo $row['total_clicks']; ?></td>
            </tr>
          <?php endwhile; ?>
        </table>
      </div>


    </div>

  </div>

</div>

<script>
  const viewsLabels = <?php echo json_encode($viewslabels); ?>;
  const viewsData = <?php echo json_encode($viewsdata); ?>;

  const bookingLabels = <?php echo json_encode($bookingdates); ?>;
  const bookingData = <?php echo json_encode($bookingtotals); ?>;

  const typeLabels = <?php echo json_encode($typelabels); ?>;
  const typeData = <?php echo json_encode($typedata); ?>;
</script>

<script type="module" src="../assets/js/firebase-init.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="assets/js/charts.js"></script>

<?php include 'includes/footer.php'; ?>