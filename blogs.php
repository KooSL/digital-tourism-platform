<?php
$pageTitle = "Travel Blog";
include 'config/db.php';
include 'includes/blog-functions.php';

/* ---------- FILTERS ---------- */
$catSlug = $_GET['category'] ?? '';
$q       = trim($_GET['q'] ?? '');

$where  = ["b.status = 1"];
$params = [];
$types  = "";

if ($catSlug !== '') {
  $where[]  = "c.slug = ?";
  $params[] = $catSlug;
  $types   .= "s";
}

if ($q !== '') {
  $where[]  = "(b.title LIKE ? OR b.excerpt LIKE ?)";
  $like     = "%$q%";
  $params[] = $like;
  $params[] = $like;
  $types   .= "ss";
}

/* ---------- PAGINATION ---------- */
$limit  = 9;
$page   = max((int)($_GET['page'] ?? 1), 1);
$offset = ($page - 1) * $limit;

$countSql = "SELECT COUNT(*) AS total FROM blogs b LEFT JOIN blog_categories c ON b.category_id = c.id WHERE " . implode(" AND ", $where);
$countStmt = $conn->prepare($countSql);
if (!empty($params)) $countStmt->bind_param($types, ...$params);
$countStmt->execute();
$totalRows  = $countStmt->get_result()->fetch_assoc()['total'];
$totalPages = max(1, ceil($totalRows / $limit));

$sql = "SELECT b.*, c.name AS category_name, c.slug AS category_slug
        FROM blogs b
        LEFT JOIN blog_categories c ON b.category_id = c.id
        WHERE " . implode(" AND ", $where) . "
        ORDER BY b.is_featured DESC, b.created_at DESC
        LIMIT $limit OFFSET $offset";

$stmt = $conn->prepare($sql);
if (!empty($params)) $stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

/* Categories for filter bar */
$categories = mysqli_query($conn, "SELECT * FROM blog_categories ORDER BY name ASC");

/* ---------- SEO META (must be set BEFORE includes/header.php) ---------- */
$metaTitle       = $catSlug ? ucwords(str_replace('-', ' ', $catSlug)) . " Articles | Digital Tourism Platform Blog" : "Travel Blog | Tips, Guides & Stories | Digital Tourism Platform";
$metaDescription = "Explore travel tips, trekking guides, festival stories and destination insights from Digital Tourism Platform's travel blog.";
$metaKeywords    = "nepal travel blog, trekking tips, travel guides, digital tourism platform";
$canonical       = "https://www.digitaltourismplatform.com/blogs" . ($catSlug ? "?category=" . urlencode($catSlug) : "");
$ogType          = "website";

include 'includes/header.php';
?>

<div class="header-wrapper">
  <?php include 'includes/topbar.php'; ?>
  <?php include 'includes/navbar.php'; ?>
</div>

<section class="page-banner">
  <div class="overlay">
    <h1><?= $catSlug ? htmlspecialchars(ucwords(str_replace('-', ' ', $catSlug))) : "Our Travel Blog" ?></h1>
    <p>Stories, tips & guides to help you plan your next journey</p>
  </div>

  <div class="container">
    <div class="filter-wrapper">
      <form method="GET" class="search-bar">
        <?php if ($catSlug): ?>
          <input type="hidden" name="category" value="<?= htmlspecialchars($catSlug) ?>">
        <?php endif; ?>
        <input type="text" name="q" placeholder="Search articles..." value="<?= htmlspecialchars($q) ?>">
        <button type="submit"><i class="fa fa-search"></i></button>
      </form>
    </div>
  </div>
</section>

<section class="blog-list-section">
  <div class="container blog-layout">

    <div class="blog-main">
      <?php if ($result->num_rows > 0): ?>
        <div class="blog-grid">
          <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <article class="blog-card <?= $row['is_featured'] ? 'featured' : '' ?>">
              <a href="blog-details?slug=<?= urlencode($row['slug']) ?>" class="blog-img">
                <img
                  src="admin/uploads/images/blogs/<?= htmlspecialchars($row['cover_image'] ?: 'default-blog.jpg') ?>"
                  alt="<?= htmlspecialchars($row['title']) ?>"
                  loading="lazy">
                <?php if ($row['is_featured']): ?>
                  <span class="featured-badge"><i class="fa-solid fa-star"></i> Featured</span>
                <?php endif; ?>
              </a>

              <div class="blog-content">
                <?php if ($row['category_name']): ?>
                  <a href="blogs?category=<?= urlencode($row['category_slug']) ?>" class="blog-category">
                    <?= htmlspecialchars($row['category_name']) ?>
                  </a>
                <?php endif; ?>

                <h2><a href="blog-details?slug=<?= urlencode($row['slug']) ?>"><?= htmlspecialchars($row['title']) ?></a></h2>

                <p class="blog-excerpt">
                  <?= htmlspecialchars($row['excerpt'] ?: autoExcerpt($row['content'])) ?>
                </p>

                <div class="blog-meta">
                  <span><i class="fa-regular fa-user"></i> <?= htmlspecialchars($row['author']) ?></span>
                  <span><i class="fa-regular fa-calendar"></i> <?= date('M d, Y', strtotime($row['created_at'])) ?></span>
                  <span><i class="fa-regular fa-clock"></i> <?= readingTime($row['content']) ?></span>
                </div>

                <a href="blog-details?slug=<?= urlencode($row['slug']) ?>" class="btn">Read More</a>
              </div>
            </article>
          <?php endwhile; ?>
        </div>

        <?php if ($totalPages > 1): ?>
          <div class="pagination">
            <?php for ($p = 1; $p <= $totalPages; $p++): ?>
              <a href="?page=<?= $p ?><?= $catSlug ? '&category=' . urlencode($catSlug) : '' ?><?= $q ? '&q=' . urlencode($q) : '' ?>"
                class="page-btn <?= $p == $page ? 'active' : '' ?>">
                <?= $p ?>
              </a>
            <?php endfor; ?>
          </div>
        <?php endif; ?>

      <?php else: ?>
        <p class="no-package">No articles found.</p>
      <?php endif; ?>
    </div>

    <aside class="blog-sidebar">
      <div class="sidebar-widget">
        <h3>Categories</h3>
        <ul class="category-list">
          <li><a href="blogs" class="<?= !$catSlug ? 'active' : '' ?>">All</a></li>
          <?php mysqli_data_seek($categories, 0);
          while ($cat = mysqli_fetch_assoc($categories)): ?>
            <li>
              <a href="blogs?category=<?= urlencode($cat['slug']) ?>" class="<?= $catSlug === $cat['slug'] ? 'active' : '' ?>">
                <?= htmlspecialchars($cat['name']) ?>
              </a>
            </li>
          <?php endwhile; ?>
        </ul>
      </div>
    </aside>

  </div>
</section>

<?php include 'includes/footer.php'; ?>