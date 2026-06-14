<?php
include '../config/db.php';
include 'auth.php';

$limit = 1;

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max($page, 1);

$offset = ($page - 1) * $limit;

// Total FAQs
$totalResult = mysqli_query(
  $conn,
  "SELECT COUNT(*) AS total FROM faqs"
);

$totalRows = mysqli_fetch_assoc($totalResult)['total'];
$totalPages = ceil($totalRows / $limit);


if (isset($_GET['delete'])) {
  $id = $_GET['delete'];
  if (mysqli_query($conn, "DELETE FROM faqs WHERE id=$id")) {
    $_SESSION['success'] = "FAQ deleted successfully.";
  } else {
    $_SESSION['error'] = "Failed to delete FAQ.";
  }
  header("Location: manage-faqs");
  exit();
}

include 'includes/header.php';
include 'includes/sidebar.php';


?>

<div class="admin-content">
  <h2>Manage FAQs</h2>

  <?php include 'includes/admin-alert.php'; ?>

  <table class="admin-table">
    <thead>
      <tr>
        <th>S.N.</th>
        <th>Created Date</th>
        <th>Question</th>
        <th>Answer</th>
        <th>Is Featured</th>
        <th>Status</th>
        <th>Action</th>

      </tr>
    </thead>

    <tbody>
      <?php
      $i = $offset + 1;

      $result = mysqli_query(
        $conn,
        "SELECT * FROM faqs
     ORDER BY id DESC
     LIMIT $limit OFFSET $offset"
      );
      while ($row = mysqli_fetch_assoc($result)) {
      ?>
        <tr>
          <td><?= $i++ ?></td>
          <td><?= $row['created_at'] ?></td>
          <td><?= $row['question'] ?></td>
          <td>
            <?= implode(' ', array_slice(explode(' ', $row['answer']), 0, 5)); ?>...
          </td>
          <?php
          if ($row['is_featured'] == 1) {
            echo '<td class="status-col published">Yes</td>';
          } else {
            echo '<td class="status-col draft">No</td>';
          }
          ?>

          <?php
          if ($row['status'] == 1) {
            echo '<td class="status-col published">Active</td>';
          } else {
            echo '<td class="status-col draft">Inactive</td>';
          }
          ?>

          <td class="action-col-flight">
            <a href="edit-faq?id=<?= $row['id'] ?>" class="btn-edit">Edit</a>
            <a href="?delete=<?= $row['id'] ?>"
              onclick="return confirm('Delete this FAQ?')"
              class="btn-delete">
              Delete
            </a>
          </td>

        </tr>
      <?php } ?>
    </tbody>
  </table>

  <?php include 'includes/admin-pagination.php'; ?>

</div>

<script src="assets/js/admin-alert.js"></script>

<?php include 'includes/footer.php'; ?>