<?php
include '../config/db.php';
include 'auth.php';

$limit = 10;

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max($page, 1);

$offset = ($page - 1) * $limit;

// Total records
$totalResult = mysqli_query($conn, "SELECT COUNT(*) AS total FROM buses");
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

  if (mysqli_query($conn, "DELETE FROM buses WHERE id=$id")) {
    $_SESSION['success'] = "Bus deleted successfully.";
  } else {
    $_SESSION['error'] = "Failed to delete bus.";
  }
  header("Location: manage-buses");
  exit;
}

include 'includes/header.php';
include 'includes/sidebar.php';


?>

<div class="admin-content">
  <h2>Manage Buses</h2>

  <?php include 'includes/admin-alert.php'; ?>

  <table class="admin-table">
    <thead>
      <tr>
        <th>S.N.</th>
        <th>created Date</th>
        <th>Bus Name</th>
        <th>Bus Number</th>
        <th>From Location</th>
        <th>To Location</th>
        <th>Travel Date</th>
        <th>Departure Time</th>
        <th>Arrival Time</th>
        <th>Price</th>
        <th>Total Seats</th>
        <th>Description</th>
        <th>Banner Image</th>
        <th>Status</th>
        <th>Action</th>

      </tr>
    </thead>

    <tbody>
      <?php
      $i = $offset + 1;

      $result = mysqli_query(
        $conn,
        "SELECT * FROM buses
     ORDER BY id DESC
     LIMIT $limit OFFSET $offset"
      );
      while ($row = mysqli_fetch_assoc($result)) {
      ?>
        <tr>
          <td><?= $i++ ?></td>
          <td><?= $row['created_at'] ?></td>
          <td><?= $row['bus_name'] ?></td>
          <td><?= $row['bus_number'] ?></td>
          <td><?= $row['from_location'] ?></td>
          <td><?= $row['to_location'] ?></td>
          <td><?= $row['travel_date'] ?></td>
          <td><?= $row['departure_time'] ?></td>
          <td><?= $row['arrival_time'] ?></td>
          <td><?= $row['price'] ?></td>
          <td><?= $row['total_seats'] ?></td>
          <td>
            <?= implode(' ', array_slice(explode(' ', $row['description']), 0, 5)); ?>...
          </td>
          <td>
            <img src="uploads/images/buses/<?= $row['banner_image'] ?>" height="50">
          </td>

          <?php
          if ($row['status'] == 1) {
            echo '<td class="status-col published">Active</td>';
          } else {
            echo '<td class="status-col draft">Inactive</td>';
          }
          ?>

          <td class="action-col">
            <a href="edit-bus?id=<?= $row['id'] ?>" class="btn-edit">Edit</a>
            <a href="javascript:void(0)"
              onclick="showConfirm('?delete=<?= $row['id'] ?>','Delete this bus?')"
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
