<?php
include '../config/db.php';
include 'auth.php';
include 'includes/header.php';
include 'includes/sidebar.php';

$albumId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$slug = $_GET['slug'] ?? '';

$limit = 10;

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max($page, 1);

$offset = ($page - 1) * $limit;

// Total photos in this album
$totalResult = mysqli_query(
  $conn,
  "SELECT COUNT(*) AS total
     FROM gallery_photos
     WHERE album_id = $albumId"
);

$totalRows = mysqli_fetch_assoc($totalResult)['total'];
$totalPages = ceil($totalRows / $limit);

// DELETE TOUR
if (isset($_GET['delete'])) {
  $photoId = (int)$_GET['delete'];

  if (mysqli_query($conn, "DELETE FROM gallery_photos WHERE id=$photoId")) {
    $_SESSION['success'] = "Photo deleted successfully.";
  } else {
    $_SESSION['error'] = "Failed to delete photo.";
  }
  header("Location: manage-photos?id=$albumId&slug=" . urlencode($slug));
  exit;
}

?>

<div class="admin-content">
  <div class="manage-photos-title-box">
    <h2>Manage Photos</h2>

    <?php include 'includes/admin-alert.php'; ?>

    <a href="add-photos" class="btn-add-new">Add New Photos</a>
  </div>

  <table class="admin-table">
    <thead>
      <tr>
        <th>S.N.</th>
        <th>created Date</th>
        <!-- <th>Album ID</th> -->
        <th>Image</th>
        <th>Action</th>

      </tr>
    </thead>

    <tbody>
      <?php
      $i = $offset + 1;

      $result = mysqli_query(
        $conn,
        "SELECT * FROM gallery_photos
     WHERE album_id = $albumId
     ORDER BY id DESC
     LIMIT $limit OFFSET $offset"
      );
      while ($row = mysqli_fetch_assoc($result)) {
      ?>
        <tr>
          <td><?= $i++ ?></td>
          <td><?= $row['created_at'] ?></td>
          <!-- <td><?= $row['album_id'] ?></td> -->

          <?php
          $slug = $_GET['slug'];
          ?>
          <td>
            <img src="uploads/gallery/<?= $slug ?>/<?= $row['image'] ?>" height="50">
          </td>

          </td>
          <td class="action-col-flight">
            <!-- <a href="edit-flight.php?id=<?= $row['id'] ?>" class="btn-edit">Edit</a> -->
            <a href="?delete=<?= $row['id'] ?>&id=<?= $albumId ?>&slug=<?= urlencode($slug) ?>"
              onclick="return confirm('Delete this Photo?')"
              class="btn-delete">
              Delete
            </a>
          </td>

        </tr>
      <?php } ?>
    </tbody>
  </table>

  <div class="pagination">

    <?php for ($p = 1; $p <= $totalPages; $p++): ?>
      <a href="?id=<?= $albumId ?>&slug=<?= urlencode($slug) ?>&page=<?= $p ?>"
        class="page-btn <?= $p == $page ? 'active' : '' ?>">
        <?= $p ?>
      </a>
    <?php endfor; ?>

  </div>

</div>

<script src="assets/js/admin-alert.js"></script>

<?php include 'includes/footer.php'; ?>