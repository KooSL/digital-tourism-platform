<?php
include '../config/db.php';
include 'auth.php';
include 'includes/header.php';
include 'includes/sidebar.php';

if (empty($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$albums = mysqli_query($conn, "SELECT * FROM gallery_albums");

if (isset($_POST['upload'])) {

  if (
    !isset($_POST['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
  ) {
    die("CSRF validation failed.");
  }

  $album_id = $_POST['album'];

  // Use prepared statement for SELECT
  $stmt = $conn->prepare("SELECT slug FROM gallery_albums WHERE id = ?");
  $stmt->bind_param("i", $album_id);
  $stmt->execute();
  $result = $stmt->get_result();
  $album = $result->fetch_assoc();
  $stmt->close();

  if (!$album) {
    die("Invalid album selected.");
  }

  $path = "uploads/gallery/" . $album['slug'] . "/";

  // Ensure directory exists
  if (!is_dir($path)) {
    mkdir($path, 0755, true);
  }

  // Use prepared statement for INSERT
  $stmt = $conn->prepare("INSERT INTO gallery_photos (album_id, image) VALUES (?, ?)");
  $stmt->bind_param("is", $album_id, $safe_name);

  foreach ($_FILES['images']['name'] as $key => $img) {
    // Sanitize file name to prevent path traversal and other issues
    $safe_name = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', basename($img));
    if (move_uploaded_file($_FILES['images']['tmp_name'][$key], $path . $safe_name)) {
      $stmt->execute();
    }
  }

  $stmt->close();
  $_SESSION['success'] = "Photos uploaded successfully.";
  header("Location: manage-photos?id=" . $album_id . "&slug=" . $album['slug']);
  exit();
}
?>

<div class="admin-content">

  <h2>Upload Photos</h2>

  <?php include 'includes/admin-alert.php'; ?>

  <form method="POST" enctype="multipart/form-data" class="admin-form">

    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

    <select name="album" required>
      <?php while ($a = mysqli_fetch_assoc($albums)) { ?>
        <option value="<?= $a['id'] ?>"><?= $a['title'] ?></option>
      <?php } ?>
    </select>

    <div class="file_input">
      <label>Select Photos</label>
      <input type="file" name="images[]" multiple accept="image/*" required>
    </div>

    <button type="submit" name="upload">Upload</button>
  </form>

</div>
<script src="assets/js/admin-alert.js"></script>

<?php include 'includes/footer.php'; ?>
