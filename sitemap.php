<?php

declare(strict_types=1);

include 'config/db.php';

header('Content-Type: application/xml; charset=utf-8');

$base = 'https://dtp.com.np';

/**
 * Escape text for XML.
 */
function xmlEscape(string $value): string
{
  return htmlspecialchars($value, ENT_XML1 | ENT_QUOTES, 'UTF-8');
}

/**
 * Create a full URL safely.
 */
function siteUrl(string $path = ''): string
{
  global $base;

  return $base . ($path !== '' && $path[0] !== '/' ? '/' : '') . $path;
}

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>

<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">

  <!-- Main Pages -->

  <url>
    <loc><?= xmlEscape(siteUrl('/')) ?></loc>
    <changefreq>daily</changefreq>
    <priority>1.0</priority>
  </url>

  <url>
    <loc><?= xmlEscape(siteUrl('/tours')) ?></loc>
    <changefreq>daily</changefreq>
    <priority>0.9</priority>
  </url>

  <url>
    <loc><?= xmlEscape(siteUrl('/blogs')) ?></loc>
    <changefreq>daily</changefreq>
    <priority>0.8</priority>
  </url>

  <url>
    <loc><?= xmlEscape(siteUrl('/buses')) ?></loc>
    <changefreq>daily</changefreq>
    <priority>0.6</priority>
  </url>

  <url>
    <loc><?= xmlEscape(siteUrl('/flights')) ?></loc>
    <changefreq>daily</changefreq>
    <priority>0.6</priority>
  </url>

  <url>
    <loc><?= xmlEscape(siteUrl('/services')) ?></loc>
    <changefreq>monthly</changefreq>
    <priority>0.6</priority>
  </url>

  <url>
    <loc><?= xmlEscape(siteUrl('/about')) ?></loc>
    <changefreq>monthly</changefreq>
    <priority>0.5</priority>
  </url>

  <url>
    <loc><?= xmlEscape(siteUrl('/contact')) ?></loc>
    <changefreq>monthly</changefreq>
    <priority>0.5</priority>
  </url>


  <!-- Published Blog Posts -->

  <?php

  $blogs = mysqli_query(
    $conn,
    "SELECT slug, updated_at
     FROM blogs
     WHERE status = 1
     ORDER BY updated_at DESC"
  );

  if ($blogs) {

    while ($row = mysqli_fetch_assoc($blogs)) {

      $slug = (string) $row['slug'];

      $blogUrl = siteUrl(
        '/blog-details?slug=' . urlencode($slug)
      );

      $lastmod = '';

      if (!empty($row['updated_at'])) {
        $timestamp = strtotime($row['updated_at']);

        if ($timestamp !== false) {
          $lastmod = date('Y-m-d', $timestamp);
        }
      }
  ?>

      <url>
        <loc><?= xmlEscape($blogUrl) ?></loc>

        <?php if ($lastmod !== ''): ?>
          <lastmod><?= xmlEscape($lastmod) ?></lastmod>
        <?php endif; ?>

        <changefreq>weekly</changefreq>
        <priority>0.7</priority>
      </url>

  <?php
    }
  } else {

    error_log('Sitemap blog query failed: ' . mysqli_error($conn));
  }
  ?>


  <!-- Blog Categories -->

  <?php

  $cats = mysqli_query(
    $conn,
    "SELECT slug
     FROM blog_categories
     WHERE slug IS NOT NULL
     AND slug != ''"
  );

  if ($cats) {

    while ($row = mysqli_fetch_assoc($cats)) {

      $categorySlug = (string) $row['slug'];

      $categoryUrl = siteUrl(
        '/blogs?category=' . urlencode($categorySlug)
      );
  ?>

      <url>
        <loc><?= xmlEscape($categoryUrl) ?></loc>
        <changefreq>weekly</changefreq>
        <priority>0.6</priority>
      </url>

  <?php
    }
  } else {

    error_log('Sitemap category query failed: ' . mysqli_error($conn));
  }
  ?>

  <!-- Published Tour Details -->

  <?php

  $tours = mysqli_query(
    $conn,
    "SELECT id, updated_at
     FROM tours
     WHERE status = 1
     ORDER BY updated_at DESC"
  );

  if ($tours) {

    while ($row = mysqli_fetch_assoc($tours)) {

      $tourId = (string) $row['id'];

      $tourUrl = siteUrl(
        '/tour-details?id=' . urlencode($tourId)
      );

      $lastmod = '';

      if (!empty($row['updated_at'])) {
        $timestamp = strtotime($row['updated_at']);

        if ($timestamp !== false) {
          $lastmod = date('Y-m-d', $timestamp);
        }
      }
  ?>

      <url>
        <loc><?= xmlEscape($tourUrl) ?></loc>

        <?php if ($lastmod !== ''): ?>
          <lastmod><?= xmlEscape($lastmod) ?></lastmod>
        <?php endif; ?>

        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
      </url>

  <?php
    }
  } else {

    error_log('Sitemap tour query failed: ' . mysqli_error($conn));
  }
  ?>

</urlset>