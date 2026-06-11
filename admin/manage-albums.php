<?php
include '../config/db.php';
include 'auth.php';
include 'includes/header.php';
include 'includes/sidebar.php';

$limit = 10;

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max($page, 1);

$offset = ($page - 1) * $limit;

// Total albums
$totalResult = mysqli_query(
  $conn,
  "SELECT COUNT(*) AS total FROM gallery_albums"
);

$totalRows = mysqli_fetch_assoc($totalResult)['total'];
$totalPages = ceil($totalRows / $limit);


// DELETE TOUR
if (isset($_GET['delete'])) {
  $id = $_GET['delete'];

  // $query = mysqli_query($conn, "SELECT image, pdf FROM tours WHERE id=$id");
  // $data = mysqli_fetch_assoc($query);

  // if($data){
  //   @unlink("assets/images/".$data['banner_image']);
  //   @unlink("assets/pdf/".$data['pdf_file']);
  // }

  if (mysqli_query($conn, "DELETE FROM gallery_albums WHERE id=$id")) {
    $_SESSION['success'] = "Album deleted successfully.";
  } else {
    $_SESSION['error'] = "Failed to delete album.";
  }
  header("Location: manage-albums");
  exit;
}
?>

<div class="admin-content">
  <h2>Manage Albums</h2>

  <?php include 'includes/admin-alert.php'; ?>

  <table class="admin-table">
    <thead>
      <tr>
        <th>S.N.</th>
        <th>created Date</th>
        <th>Title</th>
        <th>Slug</th>
        <th>Cover Image</th>
        <th>Photos</th>
        <th>Action</th>

      </tr>
    </thead>

    <tbody>
      <?php
      $i = $offset + 1;

      $result = mysqli_query(
        $conn,
        "SELECT * FROM gallery_albums
     ORDER BY id DESC
     LIMIT $limit OFFSET $offset"
      );
      while ($row = mysqli_fetch_assoc($result)) {

        $photoCount = mysqli_fetch_assoc(
          mysqli_query(
            $conn,
            "SELECT COUNT(*) AS total
         FROM gallery_photos
         WHERE album_id = {$row['id']}"
          )
        )['total'];

      ?>
        <tr>
          <td><?= $i++ ?></td>
          <td><?= $row['created_at'] ?></td>
          <td><?= $row['title'] ?></td>
          <td><?= $row['slug'] ?></td>
          <!-- <td>
          <?= implode(' ', array_slice(explode(' ', $row['description']), 0, 5)); ?>...
        </td> -->
          <td>
            <img src="uploads/gallery/<?= $row['slug'] ?>/<?= $row['cover_image'] ?>" height="50">
          </td>

          <td class="action-col-flight">
            <p><?= $photoCount ?> Photos</p>
            <br>
            <a href="manage-photos?id=<?= $row['id'] ?>&slug=<?= $row['slug'] ?>"
              class="btn-edit">
              View
            </a>
          </td>

          <td class="action-col-flight">
            <a href="edit-album?id=<?= $row['id'] ?>" class="btn-edit">Edit</a>
            <a href="?delete=<?= $row['id'] ?>"
              onclick="return confirm('Delete this album?')"
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