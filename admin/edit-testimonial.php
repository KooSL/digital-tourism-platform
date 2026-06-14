<?php
include '../config/db.php';
include 'auth.php';


if (empty($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$id = (int)$_GET['id'];
$data = mysqli_fetch_assoc(
  mysqli_query($conn, "SELECT * FROM testimonials WHERE id=$id")
);

if (isset($_POST['update'])) {

  if (
    !isset($_POST['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
  ) {
    die("CSRF validation failed.");
  }

  $name    = $_POST['name'];
  $service = $_POST['service'];
  $review  = $_POST['review'];
  $rating  = (int)$_POST['rating'];
  $status  = (int)$_POST['status'];

  $stmt = $conn->prepare("
    UPDATE testimonials SET
      name = ?,
      service = ?,
      review = ?,
      rating = ?,
      status = ?
    WHERE id = ?
  ");

  $stmt->bind_param(
    "sssiii",
    $name,
    $service,
    $review,
    $rating,
    $status,
    $id
  );

  if ($stmt->execute()) {
    $stmt->close();
    $_SESSION['success'] = "Testimonial updated successfully.";
    header("Location: manage-testimonials");
    exit();
  } else {
    $_SESSION['error'] = "Failed to update testimonial.";
    exit();
  }
}

include 'includes/header.php';
include 'includes/sidebar.php';


?>

<div class="admin-content">
  <h2>Edit Testimonial</h2>

  <?php include 'includes/admin-alert.php'; ?>

  <form method="POST" class="admin-form validate-form">

    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

    <div class="form-group">
      <input type="text" name="name"
        value="<?= $data['name'] ?>"
        placeholder="Client Name" data-validate="name">
      <small class="error"></small>
    </div>

    <div class="form-group">
      <input type="text" name="service"
        value="<?= $data['service'] ?>"
        placeholder="Service (e.g. Tour Package – Nepal)" data-validate="name">
      <small class="error"></small>
    </div>

    <div class="form-group">
      <textarea name="review" data-validate="text10"
        placeholder="Client Review"><?= $data['review'] ?></textarea>
      <small class="error"></small>
    </div>

    <label>Rating</label>
    <select name="rating">
      <?php for ($i = 5; $i >= 1; $i--): ?>
        <option value="<?= $i ?>"
          <?= ($data['rating'] == $i) ? 'selected' : '' ?>>
          <?= $i ?> ★
        </option>
      <?php endfor; ?>
    </select>

    <label>Status</label>
    <select name="status">
      <option value="1" <?= $data['status'] == 1 ? 'selected' : '' ?>>Active</option>
      <option value="0" <?= $data['status'] == 0 ? 'selected' : '' ?>>Inactive</option>
    </select>

    <button name="update">Update Testimonial</button>
  </form>
</div>

<script src="assets/js/form-validator.js"></script>
<script src="assets/js/admin-alert.js"></script>

<?php include 'includes/footer.php'; ?>