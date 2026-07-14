<?php
include 'config/db.php';
include 'includes/blog-functions.php';
include 'includes/header.php';

$slug = $_GET['slug'] ?? '';

if ($slug === '') {
  header("Location: blogs");
  exit();
}

$stmt = $conn->prepare("
    SELECT b.*, c.name AS category_name, c.slug AS category_slug
    FROM blogs b
    LEFT JOIN blog_categories c ON b.category_id = c.id
    WHERE b.slug = ? AND b.status = 1
    LIMIT 1
");
$stmt->bind_param("s", $slug);
$stmt->execute();
$blog = $stmt->get_result()->fetch_assoc();

if (!$blog) {
  header("Location: blogs");
  exit();
}


if (empty($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

/* ---------- COMMENT SUBMISSION ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_comment'])) {

  if (
    !isset($_POST['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
  ) {
    die("CSRF validation failed.");
  }

  $cName    = trim($_POST['name'] ?? '');
  $cEmail   = trim($_POST['email'] ?? '');
  $cComment = trim($_POST['comment'] ?? '');

  if ($cName !== '' && filter_var($cEmail, FILTER_VALIDATE_EMAIL) && $cComment !== '') {
    $cStmt = $conn->prepare("INSERT INTO blog_comments (blog_id, name, email, comment) VALUES (?, ?, ?, ?)");
    $cStmt->bind_param("isss", $blog['id'], $cName, $cEmail, $cComment);
    $cStmt->execute();
    $_SESSION['comment_msg'] = "Thanks! Your comment has been submitted and is awaiting approval.";
  } else {
    $_SESSION['comment_msg'] = "Please fill in a valid name, email and comment.";
  }

  header("Location: blog-details?slug=" . urlencode($slug) . "#comments");
  exit();
}

/* ---------- APPROVED COMMENTS ---------- */
$cStmt = $conn->prepare("SELECT * FROM blog_comments WHERE blog_id = ? AND status = 1 ORDER BY created_at ASC");
$cStmt->bind_param("i", $blog['id']);
$cStmt->execute();
$comments = $cStmt->get_result()->fetch_all(MYSQLI_ASSOC);
$commentCount = count($comments);

/* Track a simple view count (best-effort, non-blocking) */
mysqli_query($conn, "UPDATE blogs SET views = views + 1 WHERE id = " . (int)$blog['id']);

/* Related posts: same category, excluding current */
$related = [];
if ($blog['category_id']) {
  $rStmt = $conn->prepare("
        SELECT id, title, slug, cover_image
        FROM blogs
        WHERE category_id = ? AND id != ? AND status = 1
        ORDER BY created_at DESC
        LIMIT 3
    ");
  $rStmt->bind_param("ii", $blog['category_id'], $blog['id']);
  $rStmt->execute();
  $related = $rStmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

/* ---------- SEO META (set BEFORE includes/header.php) ---------- */
$metaTitle       = $blog['meta_title'] ?: ($blog['title'] . " | Digital Tourism Platform Blog");
$metaDescription = $blog['meta_description'] ?: autoExcerpt($blog['content'], 155);
$metaKeywords    = $blog['meta_keywords'] ?: ($blog['tags'] ?: '');
$canonical       = $blog['canonical_url'] ?: ("https://www.digitaltourismplatform.com/blog-details?slug=" . urlencode($blog['slug']));
$ogType          = "article";
$ogImage         = "https://www.digitaltourismplatform.com/admin/uploads/images/blogs/" . ($blog['cover_image'] ?: 'default-blog.jpg');

/* JSON-LD structured data: Article + Breadcrumb */
$articleSchema = [
  "@context" => "https://schema.org",
  "@type" => "Article",
  "headline" => $blog['title'],
  "description" => $metaDescription,
  "image" => [$ogImage],
  "author" => ["@type" => "Organization", "name" => $blog['author'] ?: "Digital Tourism Platform"],
  "publisher" => [
    "@type" => "Organization",
    "name" => "Digital Tourism Platform",
    "logo" => ["@type" => "ImageObject", "url" => "https://www.digitaltourismplatform.com/assets/images/logo.png"]
  ],
  "datePublished" => date('c', strtotime($blog['created_at'])),
  "dateModified" => date('c', strtotime($blog['updated_at'])),
  "commentCount" => $commentCount,
  "mainEntityOfPage" => ["@type" => "WebPage", "@id" => $canonical]
];

$breadcrumbSchema = [
  "@context" => "https://schema.org",
  "@type" => "BreadcrumbList",
  "itemListElement" => [
    ["@type" => "ListItem", "position" => 1, "name" => "Home", "item" => "https://www.digitaltourismplatform.com/"],
    ["@type" => "ListItem", "position" => 2, "name" => "Blog", "item" => "https://www.digitaltourismplatform.com/blogs"],
    ["@type" => "ListItem", "position" => 3, "name" => $blog['title'], "item" => $canonical]
  ]
];

$jsonLd = '<script type="application/ld+json">' . json_encode($articleSchema, JSON_UNESCAPED_SLASHES) . '</script>' . "\n"
  . '<script type="application/ld+json">' . json_encode($breadcrumbSchema, JSON_UNESCAPED_SLASHES) . '</script>';

?>

<div class="header-wrapper">
  <?php include 'includes/topbar.php'; ?>
  <?php include 'includes/navbar.php'; ?>
</div>

<section class="blog-details-banner">
  <div class="overlay">
    <nav class="breadcrumb" aria-label="breadcrumb">
      <a href="/Digital_Tourism_Platform">Home</a> /
      <a href="blogs">Blog</a> /
      <?php if ($blog['category_name']): ?>
        <a href="blogs?category=<?= urlencode($blog['category_slug']) ?>"><?= htmlspecialchars($blog['category_name']) ?></a> /
      <?php endif; ?>
      <span><?= htmlspecialchars($blog['title']) ?></span>
    </nav>
    <h1><?= htmlspecialchars($blog['title']) ?></h1>
    <div class="blog-meta">
      <span><i class="fa-regular fa-user"></i> <?= htmlspecialchars($blog['author']) ?></span>
      <span><i class="fa-regular fa-calendar"></i> <?= date('M d, Y', strtotime($blog['created_at'])) ?></span>
      <span><i class="fa-regular fa-clock"></i> <?= readingTime($blog['content']) ?></span>
      <span><i class="fa-regular fa-eye"></i> <?= (int)$blog['views'] ?> views</span>
    </div>
  </div>
</section>

<section class="blog-details-section">
  <div class="container blog-layout">

    <article class="blog-main blog-single">
      <img
        src="admin/uploads/images/blogs/<?= htmlspecialchars($blog['cover_image'] ?: 'default-blog.jpg') ?>"
        alt="<?= htmlspecialchars($blog['title']) ?>"
        class="cover-img"
        loading="eager">

      <div class="blog-body">
        <?= $blog['content'] /* stored as sanitized HTML from the admin rich text editor */ ?>
      </div>

      <?php if ($blog['tags']): ?>
        <div class="blog-tags">
          <?php foreach (explode(',', $blog['tags']) as $tag): ?>
            <span class="tag"><i class="fa-solid fa-tag"></i> <?= htmlspecialchars(trim($tag)) ?></span>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <div class="share-buttons">
        <span>Share:</span>
        <a target="_blank" rel="noopener" href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode($canonical) ?>"><i class="fa-brands fa-facebook"></i></a>
        <a target="_blank" rel="noopener" href="https://twitter.com/intent/tweet?url=<?= urlencode($canonical) ?>&text=<?= urlencode($blog['title']) ?>"><i class="fa-brands fa-x-twitter"></i></a>
        <a target="_blank" rel="noopener" href="https://wa.me/?text=<?= urlencode($blog['title'] . ' ' . $canonical) ?>"><i class="fa-brands fa-whatsapp"></i></a>
      </div>

      <!-- ===== COMMENTS ===== -->
      <div id="comments" class="comments-section">
        <h3><?= $commentCount ?> Comment<?= $commentCount == 1 ? '' : 's' ?></h3>

        <?php if (!empty($comments)): ?>
          <ul class="comment-list">
            <?php foreach ($comments as $c): ?>
              <li class="comment-item">
                <div class="comment-avatar"><?= strtoupper(substr($c['name'], 0, 1)) ?></div>
                <div class="comment-body">
                  <div class="comment-head">
                    <strong><?= htmlspecialchars($c['name']) ?></strong>
                    <span class="comment-date"><?= date('M d, Y', strtotime($c['created_at'])) ?></span>
                  </div>
                  <p><?= nl2br(htmlspecialchars($c['comment'])) ?></p>
                </div>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php else: ?>
          <p class="no-comments">Be the first to comment on this article.</p>
        <?php endif; ?>

        <?php if (!empty($_SESSION['comment_msg'])): ?>
          <div class="comment-alert"><?= htmlspecialchars($_SESSION['comment_msg']) ?></div>
          <?php unset($_SESSION['comment_msg']); ?>
        <?php endif; ?>

        <form method="POST" class="comment-form" action="blog-details?slug=<?= urlencode($slug) ?>#comments" id="userForm" novalidate>
          <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

          <h4>Leave a Comment</h4>

          <div class="comment-form-row">

            <div class="form-group">
              <input type="text" name="name" placeholder="Your Name" id="name">
              <small class="error"></small>
            </div>

            <div class="form-group">
              <input type="email" name="email" placeholder="Your Email" id="email">
              <small class="error"></small>
            </div>
          </div>

          <div class="form-group">
            <textarea name="comment" placeholder="Write your comment..." rows="4" id="message"></textarea>
            <small class="error"></small>
          </div>

          <button type="submit" name="submit_comment" class="btn">Post Comment</button>
          <small class="comment-note">Your comment will be visible after admin approval.</small>
        </form>
      </div>
    </article>

    <aside class="blog-sidebar">
      <?php if (!empty($related)): ?>
        <div class="sidebar-widget">
          <h3>Related Articles</h3>
          <ul class="related-list">
            <?php foreach ($related as $r): ?>
              <li>
                <a href="blog-details?slug=<?= urlencode($r['slug']) ?>">
                  <img src="admin/uploads/images/blogs/<?= htmlspecialchars($r['cover_image'] ?: 'default-blog.jpg') ?>" alt="<?= htmlspecialchars($r['title']) ?>">
                  <span><?= htmlspecialchars($r['title']) ?></span>
                </a>
              </li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <div class="sidebar-widget">
        <h3>Plan Your Trip</h3>
        <p>Found this helpful? Browse our tour packages and start planning your next adventure.</p>
        <a href="tours" class="btn">View Trips</a>
      </div>
    </aside>

  </div>
</section>

<script src="assets/js/inq-cnt-validation.js"></script>
<script src="assets/js/success-errorBox.js"></script>
<?php include 'includes/footer.php'; ?>