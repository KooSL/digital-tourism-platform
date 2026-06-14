<?php

include '../config/db.php';
include 'auth.php';

if (empty($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if (isset($_POST['create_album'])) {

  if (
    !isset($_POST['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
  ) {
    die("CSRF validation failed.");
  }


  $title = $_POST['title'];
  $slug  = strtolower(str_replace(' ', '-', $title));

  $folder = "uploads/gallery/$slug";
  if (!is_dir($folder)) {
    mkdir($folder, 0777, true);
  }

  $cover = basename($_FILES['cover']['name']);
  move_uploaded_file($_FILES['cover']['tmp_name'], "$folder/$cover");

  $stmt = mysqli_prepare($conn, "INSERT INTO gallery_albums (title, slug, cover_image) VALUES (?, ?, ?)");
  mysqli_stmt_bind_param($stmt, "sss", $title, $slug, $cover);
  mysqli_stmt_execute($stmt);
  mysqli_stmt_close($stmt);

  $_SESSION['success'] = "Album created successfully.";
  header("Location: manage-albums");
  exit();
}

include 'includes/header.php';
include 'includes/sidebar.php';


?>

<div class="admin-content">

  <h2>Add Album</h2>

  <?php include 'includes/admin-alert.php'; ?>

  <form method="POST" enctype="multipart/form-data" class="admin-form validate-form">

    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

    <div class="form-group">
      <input type="text" name="title" placeholder="Album Title" required data-validate="name">
      <small class="error"></small>
    </div>

    <div class="file_input">
      <label>Cover Image</label>
      <input type="file" name="cover" accept="image/*" required>
    </div>

    <button type="submit" name="create_album">Create Album</button>

  </form>
</div>
<script src="assets/js/form-validator.js"></script>
<script src="assets/js/admin-alert.js"></script>

<?php include 'includes/footer.php'; ?>
