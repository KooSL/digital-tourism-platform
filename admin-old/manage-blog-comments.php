<?php
include '../config/db.php';
include 'auth.php';

// APPROVE
if (isset($_GET['approve'])) {
  $id = (int)$_GET['approve'];
  mysqli_query($conn, "UPDATE blog_comments SET status = 1 WHERE id = $id");
  $_SESSION['success'] = "Comment approved.";
  header("Location: manage-blog-comments");
  exit;
}

// UNAPPROVE
if (isset($_GET['unapprove'])) {
  $id = (int)$_GET['unapprove'];
  mysqli_query($conn, "UPDATE blog_comments SET status = 0 WHERE id = $id");
  $_SESSION['success'] = "Comment hidden.";
  header("Location: manage-blog-comments");
  exit;
}

// DELETE
if (isset($_GET['delete'])) {
  $id = (int)$_GET['delete'];
  mysqli_query($conn, "DELETE FROM blog_comments WHERE id = $id");
  $_SESSION['success'] = "Comment deleted.";
  header("Location: manage-blog-comments");
  exit;
}

$limit = 10;
$page = max((int)($_GET['page'] ?? 1), 1);
$offset = ($page - 1) * $limit;

$totalRows = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM blog_comments"))['total'];
$totalPages = max(1, ceil($totalRows / $limit));

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="admin-content">
  <h2>Manage Blog Comments</h2>

  <?php include 'includes/admin-alert.php'; ?>

  <table class="admin-table">
    <thead>
      <tr>
        <th>S.N.</th>
        <th>Date</th>
        <th>Post</th>
        <th>Name</th>
        <th>Email</th>
        <th>Comment</th>
        <th>Status</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $i = $offset + 1;
      $result = mysqli_query($conn, "
        SELECT bc.*, b.title AS blog_title, b.slug AS blog_slug
        FROM blog_comments bc
        LEFT JOIN blogs b ON bc.blog_id = b.id
        ORDER BY bc.status ASC, bc.created_at DESC
        LIMIT $limit OFFSET $offset
      ");
      while ($row = mysqli_fetch_assoc($result)) {
      ?>
        <tr>
          <td><?= $i++ ?></td>
          <td><?= $row['created_at'] ?></td>
          <td>
            <a href="../blog-details?slug=<?= urlencode($row['blog_slug']) ?>#comments" target="_blank">
              <?= htmlspecialchars($row['blog_title'] ?? '(deleted post)') ?>
            </a>
          </td>
          <td><?= htmlspecialchars($row['name']) ?></td>
          <td><?= htmlspecialchars($row['email']) ?></td>
          <td><?= htmlspecialchars(mb_strimwidth($row['comment'], 0, 80, '...')) ?></td>
          <td class="status-col <?= $row['status'] == 1 ? 'published' : 'draft' ?>">
            <?= $row['status'] == 1 ? 'Approved' : 'Pending' ?>
          </td>
          <td class="action-col">
            <?php if ($row['status'] == 1): ?>
              <a href="?unapprove=<?= $row['id'] ?>" class="btn-edit">Hide</a>
            <?php else: ?>
              <a href="?approve=<?= $row['id'] ?>" class="btn-edit">Approve</a>
            <?php endif; ?>

            <a href="javascript:void(0)"
              onclick="showConfirm('?delete=<?= $row['id'] ?>','Delete this comment?')"
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
<script src="../assets/js/confirmation.js"></script>