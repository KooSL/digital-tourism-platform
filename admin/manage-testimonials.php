<?php
include '../config/db.php';
include 'auth.php';
include 'includes/header.php';
include 'includes/sidebar.php';

$limit = 10;

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max($page, 1);

$offset = ($page - 1) * $limit;

// Total testimonials
$totalResult = mysqli_query(
  $conn,
  "SELECT COUNT(*) AS total FROM testimonials"
);

$totalRows = mysqli_fetch_assoc($totalResult)['total'];
$totalPages = ceil($totalRows / $limit);

/* DELETE */
if (isset($_GET['delete'])) {
  $id = (int)$_GET['delete'];

  if (mysqli_query($conn, "DELETE FROM testimonials WHERE id=$id")) {
    $_SESSION['success'] = "Testimonial deleted successfully.";
  } else {
    $_SESSION['error'] = "Failed to delete testimonial.";
  }
  header("Location: manage-testimonials");
  exit();
}
?>

<div class="admin-content">
  <h2>Manage Testimonials</h2>

  <?php include 'includes/admin-alert.php'; ?>

  <table class="admin-table">
    <thead>
      <tr>
        <th>S.N</th>
        <th>Name</th>
        <th>Service</th>
        <th>Review</th>
        <th>Rating</th>
        <th>Status</th>
        <th>Action</th>
      </tr>
    </thead>

    <tbody>
      <?php
      $i = $offset + 1;

      $result = mysqli_query(
        $conn,
        "SELECT * FROM testimonials
     ORDER BY id DESC
     LIMIT $limit OFFSET $offset"
      );

      while ($row = mysqli_fetch_assoc($result)) {
      ?>
        <tr>
          <td><?= $i++ ?></td>
          <td><?= htmlspecialchars($row['name']) ?></td>
          <td><?= htmlspecialchars($row['service']) ?></td>
          <td>
            <?= implode(' ', array_slice(explode(' ', $row['review']), 0, 6)); ?>...
          </td>
          <td><?= $row['rating'] ?><span class="ratingstar"> ★</span></td>

          <td class="<?= $row['status'] ? 'published' : 'draft' ?>">
            <?= $row['status'] ? 'Active' : 'Inactive' ?>
          </td>

          <td>
            <a href="edit-testimonial?id=<?= $row['id'] ?>" class="btn-edit">Edit</a>
            <a href="?delete=<?= $row['id'] ?>"
              onclick="return confirm('Delete this testimonial?')"
              class="btn-delete">Delete</a>
          </td>
        </tr>
      <?php } ?>
    </tbody>
  </table>

  <?php include 'includes/admin-pagination.php'; ?>
  
</div>

<script src="assets/js/admin-alert.js"></script>

<?php include 'includes/footer.php'; ?>