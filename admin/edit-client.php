<?php
include '../config/db.php';
include 'auth.php';
include 'includes/header.php';
include 'includes/sidebar.php';

if (empty($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$id = (int)$_GET['id'];
$client = mysqli_fetch_assoc(
  mysqli_query($conn, "SELECT * FROM clients WHERE id=$id")
);

if (!$client) {
  header("Location: manage-clients");
  exit();
}

if (!extension_loaded('gd')) {
  die('GD Library is not enabled on the server.');
}


/* IMAGE RESIZE FUNCTION */
function resizeImage($source, $dest, $maxWidth = 300)
{
  list($width, $height, $type) = getimagesize($source);

  $ratio = $width / $height;
  $newWidth  = $maxWidth;
  $newHeight = $maxWidth / $ratio;

  $image_p = imagecreatetruecolor($newWidth, $newHeight);

  switch ($type) {
    case IMAGETYPE_JPEG:
      $image = imagecreatefromjpeg($source);
      break;
    case IMAGETYPE_PNG:
      $image = imagecreatefrompng($source);
      imagealphablending($image_p, false);
      imagesavealpha($image_p, true);
      break;
    default:
      return false;
  }

  imagecopyresampled(
    $image_p,
    $image,
    0,
    0,
    0,
    0,
    $newWidth,
    $newHeight,
    $width,
    $height
  );

  if ($type == IMAGETYPE_PNG) {
    imagepng($image_p, $dest);
  } else {
    imagejpeg($image_p, $dest, 85);
  }

  imagedestroy($image);
  imagedestroy($image_p);
  return true;
}

/* UPDATE CLIENT */
if (isset($_POST['update'])) {

  if (
    !isset($_POST['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
  ) {
    die("CSRF validation failed.");
  }

  $status = $_POST['status'];
  $logo   = $client['logo'];

  if (!empty($_FILES['logo']['name'])) {

    $allowedTypes = ['image/jpeg', 'image/png'];
    $fileType = $_FILES['logo']['type'];

    if (!in_array($fileType, $allowedTypes)) {
      echo "<script>alert('Only JPG & PNG allowed');</script>";
    } else {

      $ext  = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
      $logo = time() . '_client.' . $ext;
      $path = "uploads/images/clients/" . $logo;

      resizeImage($_FILES['logo']['tmp_name'], $path, 300);
      @unlink("uploads/images/clients/" . $client['logo']);
    }
  }

  $stmt = $conn->prepare("
    UPDATE clients SET logo=?, status=? WHERE id=?
  ");
  $stmt->bind_param("sii", $logo, $status, $id);

  if ($stmt->execute()) {
    $stmt->close();
    $_SESSION['success'] = "Client updated successfully.";
    header("Location: manage-clients");
    exit();
  }
}
?>

<div class="admin-content">
  <h2>Edit Client</h2>

  <form method="POST" enctype="multipart/form-data" class="admin-form validate-form">

    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

    <div class="form-group">
      <input type="text" name="name" value="<?= $client['name'] ?>" placeholder="Client Name" data-validate="name">
      <small class="error"></small>
    </div>

    <label>Current Logo</label>
    <img src="uploads/images/clients/<?= $client['logo'] ?>"
      height="80" style="margin-bottom:10px">

    <div class="file_input">
      <label>Change Logo (JPG / PNG only)</label>
      <input type="file" name="logo" accept="image/jpeg,image/png">
    </div>



    <label>Status</label>
    <select name="status">
      <option value="1" <?= $client['status'] == 1 ? 'selected' : '' ?>>Active</option>
      <option value="0" <?= $client['status'] == 0 ? 'selected' : '' ?>>Inactive</option>
    </select>

    <button name="update">Update Client</button>
  </form>
</div>

<script src="assets/js/form-validator.js"></script>
<?php include 'includes/footer.php'; ?>