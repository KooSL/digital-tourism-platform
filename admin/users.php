<?php
include '../config/db.php';
include 'auth.php';

$limit = 10;

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max($page, 1);

$offset = ($page - 1) * $limit;

// Total users
$totalResult = mysqli_query(
  $conn,
  "SELECT COUNT(*) AS total FROM users"
);

$totalRows = mysqli_fetch_assoc($totalResult)['total'];
$totalPages = ceil($totalRows / $limit);


if (isset($_GET['delete'])) {
  $id = $_GET['delete'];
  if (mysqli_query($conn, "DELETE FROM users WHERE id=$id")) {
    $_SESSION['success'] = "User deleted successfully.";
  } else {
    $_SESSION['error'] = "Failed to delete user.";
  }
  header("Location: users");
  exit;
}

include 'includes/header.php';
include 'includes/sidebar.php';


?>

<div class="admin-content">
  <h2>Users</h2>

  <?php include 'includes/admin-alert.php'; ?>

  <table class="admin-table">
    <thead>
      <tr>
        <th>S.N.</th>
        <th>Created Date</th>
        <th>Name</th>
        <th>Email</th>
        <th>Phone</th>
        <th>Password Status</th>
        <th>Action</th>
      </tr>
    </thead>

    <tbody>
      <?php
      $i = $offset + 1;

      $result = mysqli_query(
        $conn,
        "SELECT * FROM users
     ORDER BY id DESC
     LIMIT $limit OFFSET $offset"
      );
      while ($row = mysqli_fetch_assoc($result)) {
      ?>
        <tr>
          <td><?= $i++ ?></td>
          <td><?= htmlspecialchars($row['created_at']) ?></td>
          <td><?= htmlspecialchars($row['name']) ?></td>
          <td><?= htmlspecialchars($row['email']) ?></td>
          <td><?= htmlspecialchars($row['phone']) ?></td>
          <td><span style="color:green;">Encrypted</span></td>
          <td>
            <a href="javascript:void(0)"
              onclick="showConfirm('?delete=<?= $row['id'] ?>','Delete this user?')"
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
