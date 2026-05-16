<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>DTP Admin Panel</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- ADMIN CSS -->
  <link rel="stylesheet" href="assets/css/admin.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&family=Poppins:wght@500;700&display=swap" rel="stylesheet">

  <link rel="icon" href="../assets/favicon/favicon.ico" type="image/x-icon">

  <link rel="icon" type="image/png" sizes="96x96"
        href="../assets/favicon/favicon-96x96.png">

  <link rel="apple-touch-icon"
        href="../assets/favicon/apple-touch-icon.png">

  <link rel="web-app-manifest-192x192"
        href="../assets/favicon/web-app-manifest-192x192">

  <link rel="web-app-manifest-512x512"
        href="../assets/favicon/web-app-manifest-512x512.png">

</head>
<body>

<!-- TOP HEADER -->

<header class="admin-header">
  <div class="admin-header-inner">
    <h2></h2>

    <div class="admin-user">
      <!-- <span>Admin</span> -->

      <a href="/Digital_Tourism_Platform" target="_blank">Our Site</a>

      <?php if (isset($_SESSION['admin'])) { ?>
        <a href="logout">Logout</a>
      <?php } ?>

    </div>

  </div>


</header>
