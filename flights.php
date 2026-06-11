<?php include 'includes/header.php'; ?>
<div class="header-wrapper">
  <?php include 'includes/topbar.php'; ?>
  <?php include 'includes/navbar.php'; ?>
</div>

<?php include 'config/db.php'; ?>

<!-- HERO -->
<!-- <section class="page-hero">
  <div class="overlay">
    <div class="container">
      <h1>Flight Bookings</h1>
      <p>Explore our international flight routes</p>
    </div>
  </div>
</section> -->

<section class="page-banner">
  <div class="overlay">
    <h1>Flight Bookings</h1>
    <p>Explore our international flight routes with best airfare deals with professional assistance</p>
  </div>

  <div class="container">

    <div class="filter-wrapper">

      <!-- SEARCH -->
      <form method="GET" class="search-bar">
        <input type="text" name="q" placeholder="Search flights..."
          value="<?= $_GET['q'] ?? '' ?>" required>
        <button type="submit"><i class="fa fa-search"></i></button>
      </form>

      <!-- FILTER -->
      <div class="filter-dropdown">

        <button type="button" id="filterToggle" class="filter-btn">
          <i class="fa fa-sliders"></i> Filters
        </button>

        <form method="GET" class="filter-box" id="filterBox">

          <input type="hidden" name="q" value="<?= $_GET['q'] ?? '' ?>">

          <!-- <div class="filter-group">
            <select name="type">
              <option value="">All Types</option>
              <option value="domestic">Domestic</option>
              <option value="international">International</option>
            </select>
          </div>

          <div class="filter-group">
            <input type="number" name="price" placeholder="Max Price">
          </div> -->

          <div class="filter-group small">

            <label>
              <input type="checkbox" name="group_fare"
                <?= isset($_GET['group_fare']) ? 'checked' : '' ?>>
              Group Fare
            </label>

            <label>
              <input type="checkbox" name="popular"
                <?= isset($_GET['popular']) ? 'checked' : '' ?>>
              Popular
            </label>

            <label>
              <input type="checkbox" name="latest"
                <?= isset($_GET['latest']) ? 'checked' : '' ?>>
              Latest
            </label>


          </div>

          <button type="submit" class="apply-btn">Apply</button>

        </form>

      </div>

    </div>

</section>

<!-- FLIGHTS LIST -->
<section class="home-flights">
  <div class="container">

    <h2 class="section-title-flight">Available Flights</h2>
    <!-- <p class="section-subtitle">
      Best airfare deals with professional assistance
    </p> -->

    <div class="flight-grid">

      <?php
      $sql = "SELECT * FROM flights WHERE status = 1";
      $params = [];
      $types = "";

      // Search
      if (!empty($_GET['q'])) {
        $search = "%" . trim($_GET['q']) . "%";

        $sql .= " AND (
          from_city LIKE ?
          OR to_city LIKE ?
          OR description LIKE ?
      )";

        $params[] = $search;
        $params[] = $search;
        $params[] = $search;
        $types .= "sss";  
      }

      // Flight Type
      if (!empty($_GET['type'])) {
        $sql .= " AND type = ?";
        $params[] = $_GET['type'];
        $types .= "s";
      }

      // Max Price
      if (!empty($_GET['price'])) {
        $sql .= " AND price <= ?";
        $params[] = (float)$_GET['price'];
        $types .= "d";
      }

      // Popular Flights
      if (isset($_GET['popular'])) {
        $sql .= " AND is_group_fare = 1";
      }

      // Latest Flights
      if (isset($_GET['latest'])) {
        $sql .= " AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
      }

      // Group Fare
      if (isset($_GET['group_fare'])) {
        $sql .= " AND is_group_fare = 1";
      }

      $sql .= " ORDER BY id DESC";

      $stmt = $conn->prepare($sql);

      if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
      }

      $stmt->execute();
      $query = $stmt->get_result();

      if (mysqli_num_rows($query) > 0) {
        while ($flight = mysqli_fetch_assoc($query)) {
      ?>

          <div class="flight-card">
            <div class="flight-card-img">

              <?php if ($flight['is_group_fare'] == 1): ?>
                <span class="group-fare-badge">
                  <i class="fa-solid fa-users"></i> Group Fare
                </span>
              <?php endif; ?>

              <img src="admin/uploads/images/flights/<?= $flight['image']; ?>" alt="<?= htmlspecialchars($flight['from_city'] . ' to ' . $flight['to_city']); ?>">

            </div>

            <div class="flight-info">
              <!-- <h3><?= htmlspecialchars($flight['title']); ?></h3> -->
              <!-- <p><?= htmlspecialchars($flight['route']); ?></p> -->
              <h3>
                <?= htmlspecialchars($flight['from_city']); ?> →
                <?= htmlspecialchars($flight['to_city']); ?>
              </h3>

              <a href="flight-details?id=<?= $flight['id']; ?>" class="btn-primary">
                View Details
              </a>
            </div>
          </div>
      <?php
        }
      } else {
        echo "<p style='text-align:center;'>No flights available at the moment.</p>";
      }
      ?>

    </div>

  </div>
</section>

<script>
  const btn = document.getElementById("filterToggle");
  const box = document.getElementById("filterBox");

  btn.onclick = () => {
    box.classList.toggle("active");
  };

  document.addEventListener("click", (e) => {
    if (!btn.contains(e.target) && !box.contains(e.target)) {
      box.classList.remove("active");
    }
  });
</script>

<?php include 'includes/footer.php'; ?>