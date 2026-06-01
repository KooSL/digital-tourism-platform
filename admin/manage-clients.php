<?php
include '../config/db.php';
include 'auth.php';
include 'includes/header.php';
include 'includes/sidebar.php';


$clients = mysqli_query($conn, "SELECT * FROM clients ORDER BY id DESC");

/* DELETE */
if(isset($_GET['delete'])){
  $id = (int)$_GET['delete'];
  if(mysqli_query($conn, "DELETE FROM clients WHERE id=$id")){
    $_SESSION['success'] = "Client deleted successfully.";
  } else {
    $_SESSION['error'] = "Failed to delete client.";
  }
  header("Location: manage-clients");
  exit();
}

?>


<div class="admin-content">
  <h2>Manage Clients</h2>

  <?php include 'includes/admin-alert.php'; ?>

    <table class="admin-table">
    <thead>
    <tr>
        <th>S.N.</th>
        <th>Name</th>
        <th>Logo</th>
        <th>Status</th>
        <th>Action</th>
    </tr>
    </thead>
    <tbody>
    <?php $i=1; while($row=mysqli_fetch_assoc($clients)): ?>
    <tr>
        <td><?= $i++ ?></td>
        <td><?= $row['name'] ?></td>
        <td>
            <img src="uploads/images/clients/<?= $row['logo'] ?>" height="50">
        </td>
        <td><?= $row['status'] ? 'Active' : 'Inactive' ?></td>
        <td>
            <a href="edit-client?id=<?= $row['id'] ?>" class="btn-edit">Edit</a>
            <a href="?delete=<?= $row['id'] ?>"
             onclick="return confirm('Delete this client?')"
             class="btn-delete">Delete</a>
        </td>
    </tr>
    <?php endwhile; ?>
    </tbody>
    </table>

</div>

<script src="assets/js/admin-alert.js"></script>

<?php include 'includes/footer.php'; ?>
