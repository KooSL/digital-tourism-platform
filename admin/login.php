<?php

session_start();

if (isset($_SESSION['admin'])) {
  header("Location: dashboard");
  exit();
}

if (empty($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

include '../config/db.php';
include 'includes/header.php';
include 'includes/sidebar.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  if (
    !isset($_POST['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
  ) {
    die("CSRF validation failed.");
  }

  $username = mysqli_real_escape_string($conn, $_POST['username']);
  $password = $_POST['password'];

  $query = mysqli_query(
    $conn,
    "SELECT * FROM admins WHERE username='$username' LIMIT 1"
  );

  if (mysqli_num_rows($query) === 1) {

    $admin = mysqli_fetch_assoc($query);

    if (password_verify($password, $admin['password'])) {

      session_regenerate_id(true);
      $_SESSION['admin'] = $admin['username'];
      $_SESSION['last_activity'] = time();
      header("Location: dashboard");
      exit;
    } else {
      $_SESSION['login_error'] = "Invalid username or password";
    }
  } else {
    $_SESSION['login_error'] = "Invalid username or password";
  }

  header("Location: login");
  exit;
}
?>

<!DOCTYPE html>
<html>

<head>
  <title>Admin Login</title>

  <link rel="stylesheet" href="assets/admin.css">
</head>

<body class="login-body">

  <?php if (isset($_GET['expired'])): ?>
    <div class="error-box">
      Your session expired. Please login again.
    </div>
  <?php endif; ?>

  <div class="login-form">

    <form method="POST" class="login-box">
      <h2>Admin Login</h2>

      <?php
      $error = $_SESSION['login_error'] ?? '';
      unset($_SESSION['login_error']);
      ?>

      <?php if ($error): ?>
        <p class="error"><?= $error ?></p>
      <?php endif; ?>

      <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

      <input type="text" name="username" placeholder="Username" required>
      <input type="password" name="password" placeholder="Password" required>
      <button name="login">Login</button>
    </form>

  </div>

</body>

</html>