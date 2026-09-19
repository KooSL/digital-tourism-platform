<?php
$pageTitle = "Tours";
include 'includes/header.php'; ?>

<div class="header-wrapper">
  <?php include 'includes/topbar.php'; ?>
  <?php include 'includes/navbar.php'; ?>
</div>

<?php include 'config/db.php';

$type = $_GET['type'] ?? '';

$where = ["status = 1"];
$params = [];
$types = "";

// TOUR TYPE
if ($type === 'domestic' || $type === 'international') {
  $where[] = "type = ?";
  $params[] = $type;
  $types .= "s";
}

// SEARCH
if (!empty($_GET['q'])) {
  $where[] = "title LIKE ?";
  $params[] = "%" . $_GET['q'] . "%";
  $types .= "s";
}

// DAYS
if (!empty($_GET['days'])) {
  $where[] = "duration <= ?";
  $params[] = (int)$_GET['days'];
  $types .= "i";
}

// PRICE
if (!empty($_GET['price'])) {
  $where[] = "price <= ?";
  $params[] = (int)$_GET['price'];
  $types .= "i";
}

// POPULAR
if (isset($_GET['popular'])) {
  $where[] = "is_popular = 1";
}

// LATEST
if (isset($_GET['latest'])) {
  $where[] = "created_at >= NOW() - INTERVAL 7 DAY";
}

/* ---------- PAGINATION ---------- */

$limit = 8;

$page = max((int)($_GET['page'] ?? 1), 1);

$offset = ($page - 1) * $limit;


/* ---------- COUNT TOTAL RESULTS ---------- */

$countSql = "
    SELECT COUNT(*) AS total
    FROM tours
    WHERE " . implode(" AND ", $where);

$countStmt = $conn->prepare($countSql);

if (!empty($params)) {
  $countStmt->bind_param($types, ...$params);
}

$countStmt->execute();

$totalRows = $countStmt
  ->get_result()
  ->fetch_assoc()['total'];

$totalPages = max(1, ceil($totalRows / $limit));


/* Prevent invalid page numbers */

if ($page > $totalPages) {
  $page = $totalPages;
  $offset = ($page - 1) * $limit;
}


/* ---------- GET TOUR RESULTS ---------- */

$sql = "
    SELECT *
    FROM tours
    WHERE " . implode(" AND ", $where) . "
    ORDER BY is_popular DESC, created_at DESC
    LIMIT ? OFFSET ?
";

$stmt = $conn->prepare($sql);


/* Add pagination parameters */

$queryParams = $params;
$queryTypes = $types;

$queryParams[] = $limit;
$queryTypes .= "i";

$queryParams[] = $offset;
$queryTypes .= "i";


$stmt->bind_param(
  $queryTypes,
  ...$queryParams
);

$stmt->execute();

$result = $stmt->get_result();
?>

<section class="page-banner">

  <?php if (isset($_GET['success'])): ?>
    <div class="success-box" id="successBox">
      <?php
      if ($_GET['success'] === 'sent') echo "Your inquiry has been sent successfully. We’ll contact you soon.";
      ?>
    </div>
  <?php endif; ?>

  <?php if (isset($_GET['error'])): ?>
    <div class="error-box" id="errorBox">
      <?php
      if ($_GET['error'] === 'invalid') echo "Invalid request. Please try again.";
      if ($_GET['error'] === 'not_found') echo "Trip not found. Please try again.";
      ?>
    </div>
  <?php endif; ?>

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

        <input type="hidden" name="type" value="<?= htmlspecialchars($type) ?>">

        <input type="text"
          name="q"
          placeholder="Search packages..."
          value="<?= $_GET['q'] ?? '' ?>"
          required>

        <button type="submit">
          <i class="fa fa-search"></i>
        </button>

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

        $tripId = $row['id'];
        $avg = mysqli_query(
          $conn,
          "SELECT
          ROUND(AVG(rating),1) AS avg_rating,
          COUNT(*) AS total_reviews
          FROM trip_reviews
          WHERE trip_id = $tripId
          AND status = 1"
        );

        $ratingData = mysqli_fetch_assoc($avg);

    ?>
        <div class="tour-row">

          <div class="tour-img">


            <img src="uploads/images/tours/<?= $row['banner_image'] ?>"
              alt="<?= $row['title'] ?>">
          </div>

          <div class="tour-details">

            <div class="badges-container">
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

                <?php if (!empty($row['old_price'])):
                  $discount = round((($row['old_price'] - $row['price']) / $row['old_price']) * 100);
                ?>
                  <span class="discount-badge trips">
                    <?= $discount ?>% OFF
                  </span>
                <?php endif; ?>

              </div>

              <div class="rating-summary trips">
                <a href="tour-details?trip=<?= $row['slug'] ?>&type=<?= $row['type'] ?>#reviews"><i class="fa-solid fa-star"></i> <?= $ratingData['avg_rating'] ?? '0.0' ?>
                  (<?= $ratingData['total_reviews'] ?> reviews)</a>
              </div>

            </div>


            <h3><?= $row['title'] ?></h3>
            <p class="duration"><i class="fa-solid fa-clock"></i> <?= $row['duration'] ?></p>
            <?php if ($row['type'] === 'domestic') : ?>
              <p class="desc">
                Experience the best of Nepal with this carefully designed tour package.
              </p>
            <?php else : ?>
              <p class="desc">
                Explore the world with our exclusive international tour package.
              </p>
            <?php endif; ?>

            <!-- <span class="price">From: <span class="price-num"> NPR <?= $row['price'] ?> | USD $<?= $row['price_usd'] ?></span></span> -->

            <p class="current-price price">
              <span class="from">From:</span>
              NPR <?= $row['price'] ?>
              <span>| USD $<?= $row['price_usd'] ?> PP</span>
            </p>

            <a href="tour-details?trip=<?= $row['slug'] ?>&type=<?= $row['type'] ?>" class="btn">
              View Details
            </a>
          </div>

        </div>
      <?php } ?>
    <?php else: ?>
      <p class="no-package">No package found.</p>
    <?php endif; ?>

  </div>

  <?php if ($totalPages > 1): ?>
    <div class="pagination trips">
      <?php if ($page > 1): ?>
        <?php
        $previousParams = $_GET;
        $previousParams['page'] = $page - 1;
        ?>
        <a
          href="?<?= htmlspecialchars(http_build_query($previousParams)) ?>"
          class="page-btn page-prev">
          <i class="fa-solid fa-chevron-left"></i>
        </a>
      <?php endif; ?>

      <?php for ($p = 1; $p <= $totalPages; $p++): ?>
        <?php
        $pageParams = $_GET;
        $pageParams['page'] = $p;
        ?>
        <a
          href="?<?= htmlspecialchars(http_build_query($pageParams)) ?>"
          class="page-btn <?= $p == $page ? 'active' : '' ?>">
          <?= $p ?>
        </a>
      <?php endfor; ?>

      <?php if ($page < $totalPages): ?>
        <?php
        $nextParams = $_GET;
        $nextParams['page'] = $page + 1;
        ?>
        <a
          href="?<?= htmlspecialchars(http_build_query($nextParams)) ?>"
          class="page-btn page-next">
          <i class="fa-solid fa-chevron-right"></i>
        </a>
      <?php endif; ?>
    </div>
  <?php endif; ?>

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

<script src="assets/js/success-errorBox.js"></script>

<?php include 'includes/footer.php'; ?>