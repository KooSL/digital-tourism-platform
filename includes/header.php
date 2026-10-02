<?php
require_once __DIR__ . '/security.php';
?>

<!DOCTYPE html>
<html lang="en" class="no-js">
<script>
      document.documentElement.classList.remove('no-js');
</script>

<head>
      <meta charset="UTF-8">

      <title><?= isset($metaTitle) ? htmlspecialchars($metaTitle) : (isset($pageTitle) ? htmlspecialchars($pageTitle) . " | Digital Tourism Platform" : "Digital Tourism Platform") ?></title>

      <meta name="description" content="<?= isset($metaDescription) ? htmlspecialchars($metaDescription) : 'Explore the Digital Tourism Platform for an immersive travel experience. Discover destinations, plan trips, and connect with fellow travelers.' ?>">

      <meta name="keywords" content="<?= isset($metaKeywords) ? htmlspecialchars($metaKeywords) : 'digital tourism, travel platform, immersive experiences, trip planning, travel community' ?>">

      <meta name="viewport" content="width=device-width, initial-scale=1.0">

      <meta name="robots" content="<?= isset($metaRobots) ? htmlspecialchars($metaRobots) : 'index, follow' ?>">

      <?php if (isset($canonical)): ?>
            <link rel="canonical" href="<?= htmlspecialchars($canonical) ?>">
      <?php endif; ?>

      <?php if (isset($metaTitle) || isset($pageTitle) || isset($metaKeywords) || isset($metaDescription)): ?>
            <meta property="og:type" content="<?= isset($ogType) ? htmlspecialchars($ogType) : 'website' ?>">
            <meta property="og:title" content="<?= htmlspecialchars($metaTitle ?? $pageTitle ?? 'Digital Tourism Platform') ?>">
            <?php if (isset($metaDescription)): ?>
                  <meta property="og:description" content="<?= htmlspecialchars($metaDescription) ?>">
            <?php endif; ?>
            <?php if (isset($canonical)): ?>
                  <meta property="og:url" content="<?= htmlspecialchars($canonical) ?>">
            <?php endif; ?>
            <?php if (isset($ogImage)): ?>
                  <meta property="og:image" content="<?= htmlspecialchars($ogImage) ?>">
            <?php endif; ?>
            <meta name="twitter:card" content="summary_large_image">
            <meta name="twitter:title" content="<?= htmlspecialchars($metaTitle ?? $pageTitle ?? 'Digital Tourism Platform') ?>">
            <?php if (isset($metaDescription)): ?>
                  <meta name="twitter:description" content="<?= htmlspecialchars($metaDescription) ?>">
            <?php endif; ?>
      <?php endif; ?>

      <?php if (isset($jsonLd)): ?>
            <?= $jsonLd ?>
      <?php endif; ?>

      <link rel="stylesheet" href="assets/css/style.css">
      <link rel="stylesheet" href="assets/css/variables.css">
      <link rel="stylesheet" href="assets/css/global.css">
      <link rel="stylesheet" href="assets/css/trip-details.css">
      <link rel="stylesheet" href="assets/css/search-filter.css">
      <link rel="stylesheet" href="assets/css/pagination.css">
      <link rel="stylesheet" href="assets/css/auth-form.css">
      <link rel="stylesheet" href="assets/css/chatbot.css">
      <link rel="stylesheet" href="assets/css/table.css">
      <link rel="stylesheet" href="assets/css/booking.css">
      <link rel="stylesheet" href="assets/css/badges.css">
      <link rel="stylesheet" href="assets/css/reviews.css">
      <link rel="stylesheet" href="assets/css/confirmation-box.css">
      <link rel="stylesheet" href="assets/css/home.css">
      <link rel="stylesheet" href="assets/css/blog.css">
      <link rel="stylesheet" href="assets/css/anti-flicker.css">
      <link rel="stylesheet" href="assets/css/successError.css">
      <link rel="stylesheet" href="assets/css/booking-details.css">


      <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css">
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">

      <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&family=Poppins:wght@500;700&display=swap" rel="stylesheet">
      <link rel="preconnect" href="https://fonts.googleapis.com">
      <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

</head>

<body>