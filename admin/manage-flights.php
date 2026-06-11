<?php
include '../config/db.php';
include 'auth.php';
include 'includes/header.php';
include 'includes/sidebar.php';

$limit = 10;

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max($page, 1);

$offset = ($page - 1) * $limit;

// Total records
$totalResult = mysqli_query($conn, "SELECT COUNT(*) AS total FROM flights");
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

  if (mysqli_query($conn, "DELETE FROM flights WHERE id=$id")) {
    $_SESSION['success'] = "Flight deleted successfully.";
  } else {
    $_SESSION['error'] = "Failed to delete flight.";
  }
  header("Location: manage-flights");
  exit;
}
?>

<div class="admin-content">
  <h2>Manage Flights Post</h2>

  <?php include 'includes/admin-alert.php'; ?>

  <table class="admin-table">
    <thead>
      <tr>
        <th>S.N.</th>
        <th>created Date</th>
        <th>From City</th>
        <th>To City</th>
        <th>Description</th>
        <th>Image</th>
        <th>Group Fare</th>
        <th>Status</th>
        <th>Action</th>

      </tr>
    </thead>

    <tbody>
      <?php
      $i = $offset + 1;

      $result = mysqli_query(
        $conn,
        "SELECT * FROM flights
     ORDER BY id DESC
     LIMIT $limit OFFSET $offset"
      );
      while ($row = mysqli_fetch_assoc($result)) {
      ?>
        <tr>
          <td><?= $i++ ?></td>
          <td><?= $row['created_at'] ?></td>
          <td><?= $row['from_city'] ?></td>
          <td><?= $row['to_city'] ?></td>
          <td>
            <?= implode(' ', array_slice(explode(' ', $row['description']), 0, 5)); ?>...
          </td>
          <td>
            <img src="uploads/images/flights/<?= $row['image'] ?>" height="50">
          </td>

          <?php
          if ($row['is_group_fare'] == 1) {
            echo '<td>Yes</td>';
          } else {
            echo '<td>No</td>';
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
            <a href="edit-flight?id=<?= $row['id'] ?>" class="btn-edit">Edit</a>
            <a href="?delete=<?= $row['id'] ?>"
              onclick="return confirm('Delete this flight?')"
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