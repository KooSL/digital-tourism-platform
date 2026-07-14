<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
      <meta charset="UTF-8">
      <!-- <title>Digital Tourism Platform</title> -->
       <title><?= isset($metaTitle) ? htmlspecialchars($metaTitle) : (isset($pageTitle) ? htmlspecialchars($pageTitle) . " | Digital Tourism Platform" : "Digital Tourism Platform") ?></title>
      <meta name="viewport" content="width=device-width, initial-scale=1.0">

      <?php if (isset($metaDescription)): ?>
      <meta name="description" content="<?= htmlspecialchars($metaDescription) ?>">
      <?php endif; ?>

      <?php if (isset($metaKeywords)): ?>
      <meta name="keywords" content="<?= htmlspecialchars($metaKeywords) ?>">
      <?php endif; ?>

      <meta name="robots" content="<?= isset($metaRobots) ? htmlspecialchars($metaRobots) : 'index, follow' ?>">

      <?php if (isset($canonical)): ?>
      <link rel="canonical" href="<?= htmlspecialchars($canonical) ?>">
      <?php endif; ?>

      <?php if (isset($metaTitle) || isset($metaDescription)): ?>
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
      <link rel="stylesheet" href="assets/css/tour-details.css">
      <link rel="stylesheet" href="assets/css/search-filter.css">
      <link rel="stylesheet" href="assets/css/pagination.css">
      <link rel="stylesheet" href="assets/css/auth-form.css">
      <link rel="stylesheet" href="assets/css/chatbot.css">
      <link rel="stylesheet" href="assets/css/table.css">
      <link rel="stylesheet" href="assets/css/booking.css">
      <link rel="stylesheet" href="assets/css/discount-badge.css">
      <link rel="stylesheet" href="assets/css/reviews.css">
      <link rel="stylesheet" href="assets/css/confirmation-box.css">
      <link rel="stylesheet" href="assets/css/home.css">
      <link rel="stylesheet" href="assets/css/blog.css">

      <link rel="stylesheet"
            href="https://unpkg.com/leaflet/dist/leaflet.css">

      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
      <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&family=Poppins:wght@500;700&display=swap" rel="stylesheet">
      <link rel="preconnect" href="https://fonts.googleapis.com">
      <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
      <!-- <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet"> -->
      <!-- <link href="https://fonts.googleapis.com/css2?family=Comic+Neue:wght@400;700&display=swap" rel="stylesheet"> -->

      <!-- <link rel="icon" href="assets/favicon/favicon.ico" type="image/x-icon">

      <link rel="icon" type="image/png" sizes="96x96"
            href="assets/favicon/favicon-96x96.png">

      <link rel="apple-touch-icon"
            href="assets/favicon/apple-touch-icon.png">

      <link rel="web-app-manifest-192x192"
            href="assets/favicon/web-app-manifest-192x192">

      <link rel="web-app-manifest-512x512"
            href="assets/favicon/web-app-manifest-512x512.png"> -->

</head>

<body>