<?php
include 'config/db.php';
header('Content-Type: application/xml; charset=utf-8');

$base = "https://www.digitaltourismplatform.com";

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">

  <url><loc><?= $base ?>/</loc><changefreq>daily</changefreq><priority>1.0</priority></url>
  <url><loc><?= $base ?>/tours</loc><changefreq>daily</changefreq><priority>0.9</priority></url>
  <url><loc><?= $base ?>/blogs</loc><changefreq>daily</changefreq><priority>0.8</priority></url>
  <url><loc><?= $base ?>/services</loc><changefreq>monthly</changefreq><priority>0.6</priority></url>
  <url><loc><?= $base ?>/about</loc><changefreq>monthly</changefreq><priority>0.5</priority></url>
  <url><loc><?= $base ?>/contact</loc><changefreq>monthly</changefreq><priority>0.5</priority></url>

  <?php
  $blogs = mysqli_query($conn, "SELECT slug, updated_at FROM blogs WHERE status = 1 ORDER BY updated_at DESC");
  while ($row = mysqli_fetch_assoc($blogs)):
  ?>
  <url>
    <loc><?= $base ?>/blog-details?slug=<?= urlencode($row['slug']) ?></loc>
    <lastmod><?= date('Y-m-d', strtotime($row['updated_at'])) ?></lastmod>
    <changefreq>weekly</changefreq>
    <priority>0.7</priority>
  </url>
  <?php endwhile; ?>

  <?php
  $cats = mysqli_query($conn, "SELECT slug FROM blog_categories");
  while ($row = mysqli_fetch_assoc($cats)):
  ?>
  <url>
    <loc><?= $base ?>/blogs?category=<?= urlencode($row['slug']) ?></loc>
    <changefreq>weekly</changefreq>
    <priority>0.6</priority>
  </url>
  <?php endwhile; ?>

</urlset>
