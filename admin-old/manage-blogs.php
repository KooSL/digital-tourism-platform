<?php
include '../config/db.php';
include 'auth.php';

$limit = 8;

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max($page, 1);

$offset = ($page - 1) * $limit;

$totalResult = mysqli_query($conn, "SELECT COUNT(*) AS total FROM blogs");
$totalRows = mysqli_fetch_assoc($totalResult)['total'];

$totalPages = max(1, ceil($totalRows / $limit));

// DELETE BLOG
if (isset($_GET['delete'])) {
  $id = (int)$_GET['delete'];

  $query = mysqli_query($conn, "SELECT cover_image FROM blogs WHERE id=$id");
  $img = mysqli_fetch_assoc($query);

  if ($img && $img['cover_image']) {
    @unlink("uploads/images/blogs/" . $img['cover_image']);
  }

  if (mysqli_query($conn, "DELETE FROM blogs WHERE id=$id")) {
    $_SESSION['success'] = "Blog post deleted successfully.";
  } else {
    $_SESSION['error'] = "Failed to delete blog post.";
  }
  header("Location: manage-blogs");
  exit;
}

include 'includes/header.php';
include 'includes/sidebar.php';

?>

<div class="admin-content">
  <div class="manage-photos-title-box">
    <h2>Manage Blogs</h2>

    <?php include 'includes/admin-alert.php'; ?>

    <a href="add-blog" class="btn-add-new">Add New Blog</a>

  </div>


  <table class="admin-table">
    <thead>
      <tr>
        <th>S.N.</th>
        <th>Date</th>
        <th>Cover</th>
        <th>Title</th>
        <th>Category</th>
        <th>Author</th>
        <th>Views</th>
        <th>Featured</th>
        <th>Status</th>
        <th>Action</th>
      </tr>
    </thead>

    <tbody>
      <?php
      $i = $offset + 1;

      $result = mysqli_query(
        $conn,
        "SELECT b.*, c.name AS category_name
         FROM blogs b
         LEFT JOIN blog_categories c ON b.category_id = c.id
         ORDER BY b.id DESC
         LIMIT $limit OFFSET $offset"
      );

      while ($row = mysqli_fetch_assoc($result)) {
      ?>
        <tr>
          <td><?= $i++ ?></td>
          <td><?= $row['created_at'] ?></td>
          <td>
            <img src="uploads/images/blogs/<?= htmlspecialchars($row['cover_image']) ?>" height="50">
          </td>
          <td><?= htmlspecialchars($row['title']) ?></td>
          <td><?= htmlspecialchars($row['category_name'] ?? '-') ?></td>
          <td><?= htmlspecialchars($row['author']) ?></td>
          <td><?= (int)$row['views'] ?></td>
          <td><?= $row['is_featured'] ? '<span class="badge badge-popular">Yes</span>' : 'No'; ?></td>

          <td class="status-col">
            <a href="javascript:void(0)"
              onclick="showConfirm('toggle-blog?id=<?= $row['id'] ?>','<?= $row['status'] == 1 ? 'Unpublish' : 'Publish' ?> this post?')"
              class="status-col <?= $row['status'] == 1 ? 'published' : 'draft' ?>">
              <?= $row['status'] == 1 ? 'Published' : 'Draft' ?>
            </a>
          </td>

          <td class="action-col">
            <a href="../blog-details?slug=<?= urlencode($row['slug']) ?>" target="_blank" class="btn-edit">View</a>
            <a href="edit-blog?id=<?= $row['id'] ?>" class="btn-edit">Edit</a>

            <a href="javascript:void(0)"
              onclick="showConfirm('?delete=<?= $row['id'] ?>','Delete this blog post?')"
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