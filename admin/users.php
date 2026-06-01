<?php

include 'auth.php';
include 'includes/header.php';
include 'includes/sidebar.php';
include '../config/db.php';


if(isset($_GET['delete'])){
  $id = $_GET['delete'];
  if(mysqli_query($conn, "DELETE FROM users WHERE id=$id")){
    $_SESSION['success'] = "User deleted successfully.";
  } else {
    $_SESSION['error'] = "Failed to delete user.";
  }
  header("Location: users");
  exit;
}
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
        <th>Password</th>
        <th>Action</th>
      </tr>
    </thead>

    <tbody>
      <?php
      $i = 1;
      $result = mysqli_query($conn, "SELECT * FROM users ORDER BY id DESC");
      while($row = mysqli_fetch_assoc($result)){
      ?>
      <tr>
        <td><?= $i++ ?></td>
        <td><?= htmlspecialchars($row['created_at']) ?></td>
        <td><?= htmlspecialchars($row['name']) ?></td>
        <td><?= htmlspecialchars($row['email']) ?></td>
        <td><?= htmlspecialchars($row['phone']) ?></td>
        <td><?= htmlspecialchars($row['password']) ?></td>
        <td>
          <a href="?delete=<?= $row['id'] ?>"
             class="btn-delete"
             onclick="return confirm('Delete this user?')">
            Delete
          </a>
        </td>
      </tr>
      <?php } ?>
    </tbody>
  </table>
</div>

<script src="assets/js/admin-alert.js"></script>

<?php include 'includes/footer.php'; ?>
