<?php include 'includes/header.php'; ?>

<div class="header-wrapper">
  <?php include 'includes/topbar.php'; ?>
  <?php include 'includes/navbar.php'; ?>
</div>

<?php include 'config/db.php';

$type = $_GET['type'] ?? '';

$where = ["status = 1"];
$params = [];
$types = "";

// SEARCH
if (!empty($_GET['q'])) {
  $where[] = "(title LIKE ?)";
  $search = "%" . $_GET['q'] . "%";
  $params[] = $search;
  $types .= "s";
}

// TYPE
if (!empty($_GET['type'])) {
  $where[] = "type = ?";
  $params[] = $_GET['type'];
  $types .= "s";
}

// DAYS
if (!empty($_GET['days'])) {
  $where[] = "duration <= ?";
  $params[] = $_GET['days'];
  $types .= "i";
}

// PRICE
if (!empty($_GET['price'])) {
  $where[] = "price <= ?";
  $params[] = $_GET['price'];
  $types .= "i";
}

// POPULAR
if (isset($_GET['popular'])) {
  $where[] = "is_popular = 1";
}

// LATEST (last 7 days)
if (isset($_GET['latest'])) {
  $where[] = "created_at >= NOW() - INTERVAL 7 DAY";
}

// FINAL QUERY
$sql = "SELECT * FROM tours WHERE " . implode(" AND ", $where);
$sql .= " ORDER BY is_popular DESC, created_at DESC";

$stmt = $conn->prepare($sql);

if (!empty($params)) {
  $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<section class="page-banner">
  <div class="overlay">
    <?php if ($type) : ?>
      <h1>Our <?php echo ucfirst($type); ?> Packages</h1>
    <?php else : ?>
      <h1>Our All Packages</h1>
    <?php endif; ?>
    <p>Explore Nepal & beyond through digital tourism platform</p>
  </div>

  <div class="container">

    <div class="filter-wrapper">

      <!-- SEARCH -->
      <form method="GET" class="search-bar">
        <input type="text" name="q" placeholder="Search packages..."
          value="<?= $_GET['q'] ?? '' ?>">
        <button type="submit"><i class="fa fa-search"></i></button>
      </form>

      <!-- FILTER -->
      <div class="filter-dropdown">

        <button type="button" id="filterToggle" class="filter-btn">
          <i class="fa fa-sliders"></i> Filters
        </button>

        <form method="GET" class="filter-box" id="filterBox">

          <input type="hidden" name="q" value="<?= $_GET['q'] ?? '' ?>">

          <div class="filter-group">
            <select name="type">
              <option value="">All Types</option>
              <option value="domestic">Domestic</option>
              <option value="international">International</option>
            </select>
          </div>

          <div class="filter-group">
            <input type="number" name="price" placeholder="Max Price">
          </div>

          <div class="filter-group small">
            <label><input type="checkbox" name="popular"> Popular</label>
            <label><input type="checkbox" name="latest"> Latest</label>
          </div>

          <button type="submit" class="apply-btn">Apply</button>

        </form>

      </div>

    </div>

</section>

<section class="tour-list-section">
  <div class="container">

    <?php
    if ($result->num_rows > 0):
      while ($row = mysqli_fetch_assoc($result)) {

        if ($type && $row['type'] !== $type) {
          continue;
        }
    ?>
        <div class="tour-row">

          <div class="tour-img">


            <img src="admin/uploads/images/tours/<?= $row['banner_image'] ?>"
              alt="<?= $row['title'] ?>">
          </div>

          <div class="tour-details">

            <div class="tour-badges">
              <?php if (!$type && in_array($row['type'], ['domestic', 'international'])): ?>
                <span class="type-badge">
                  <i class="fa-solid 
                <?= $row['type'] === 'domestic' ? 'fa-house' : 'fa-earth-americas' ?>"></i>
                  <?= ucfirst($row['type']) ?>
                </span>
              <?php endif; ?>

              <?php if ($row['is_popular'] == 1) { ?>
                <span class="popular-badge"><i class="fa-solid fa-fire"></i> Popular</span>
              <?php } ?>

              <?php if (strtotime($row['created_at']) >= strtotime('-7 days')): ?>
                <span class="latest-badge">
                  <i class="fa-solid fa-star"></i> Latest
                </span>
              <?php endif; ?>
            </div>


            <h3><?= $row['title'] ?></h3>
            <p class="duration"><?= $row['duration'] ?></p>
            <?php if ($row['type'] === 'domestic') : ?>
              <p class="desc">
                Experience the best of Nepal with this carefully designed tour package.
              </p>
            <?php else : ?>
              <p class="desc">
                Explore the world with our exclusive international tour package.
              </p>
            <?php endif; ?>


            <span class="price">From: <span class="price-num"> NPR <?= $row['price'] ?> | USD $<?= $row['price_usd'] ?></span></span>

            <a href="tour-details?id=<?= $row['id'] ?>" class="btn">
              View Details
            </a>
          </div>

        </div>
      <?php } ?>
    <?php else: ?>
      <p class="no-package">No package found.</p>
    <?php endif; ?>

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
