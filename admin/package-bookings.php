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
$totalResult = mysqli_query(
  $conn,
  "SELECT COUNT(*) AS total FROM package_bookings"
);

$totalRows = mysqli_fetch_assoc($totalResult)['total'];
$totalPages = ceil($totalRows / $limit);


// DELETE TOUR
if (isset($_GET['delete'])) {
  $id = $_GET['delete'];

  if (mysqli_query($conn, "DELETE FROM package_bookings WHERE id=$id")) {
    $_SESSION['success'] = "Package booking deleted successfully.";
  } else {
    $_SESSION['error'] = "Failed to delete package booking.";
  }
  header("Location: package-bookings");
  exit;
}
?>

<div class="admin-content">
  <h2>Package Bookings</h2>

  <?php include 'includes/admin-alert.php'; ?>

  <table class="admin-table">
    <thead>
      <tr>
        <th>S.N.</th>
        <th>Booked Date</th>
        <th>Package Name</th>
        <th>User ID</th>
        <th>Name</th>
        <th>Email</th>
        <th>Phone</th>
        <th>Travel Date</th>
        <th>Persons</th>
        <th>Payment Status</th>
        <th>Payment Method</th>
        <th>Transaction ID</th>
        <th>Status</th>
        <th>Action</th>

      </tr>
    </thead>

    <tbody>
      <?php
      $i = $offset + 1;

      $result = mysqli_query(
        $conn,
        "SELECT pb.*,
            t.title AS package_title,
            u.name AS user_name
     FROM package_bookings pb
     LEFT JOIN tours t ON pb.package_id = t.id
     LEFT JOIN users u ON pb.user_id = u.id
     ORDER BY pb.id DESC
     LIMIT $limit OFFSET $offset"
      );
      
      while ($row = mysqli_fetch_assoc($result)) {  
      ?>
        <tr>
          <td><?= $i++ ?></td>
          <td><?= $row['created_at'] ?></td>
          <td><?= htmlspecialchars($row['package_title']) ?></td>
          <td><?= $row['user_id'] ?></td>
          <td><?= $row['name'] ?></td>
          <td><?= $row['email'] ?></td>
          <td><?= $row['phone'] ?></td>
          <td><?= $row['travel_date'] ?></td>
          <td><?= $row['persons'] ?></td>
          <td><?= $row['payment_status'] ?></td>
          <td><?= $row['payment_method'] ?></td>
          <td><?= $row['transaction_id'] ?></td>
          <td><?= $row['status'] ?></td>

          <!-- <?php
                if ($row['status'] == 1) {
                  echo '<td class="status-col published">Active</td>';
                } else {
                  echo '<td class="status-col draft">Inactive</td>';
                }
                ?> -->

          <td class="action-col">
            <a href="edit-package-booking?id=<?= $row['id'] ?>" class="btn-edit">Edit</a>
            <a href="?delete=<?= $row['id'] ?>"
              onclick="return confirm('Delete this package booking?')"
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