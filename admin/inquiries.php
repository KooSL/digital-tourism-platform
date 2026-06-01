<?php

include 'auth.php';
include 'includes/header.php';
include 'includes/sidebar.php';
include '../config/db.php';


// DELETE INQUIRY
if (isset($_GET['delete'])) {
  $id = $_GET['delete'];
  if (mysqli_query($conn, "DELETE FROM inquiries WHERE id=$id")) {
    $_SESSION['success'] = "Inquiry deleted successfully.";
  } else {
    $_SESSION['error'] = "Failed to delete inquiry.";
  }
  header("Location: inquiries");
  exit;
}
?>

<div class="admin-content">
  <h2>Inquiries</h2>

  <?php include 'includes/admin-alert.php'; ?>

  <table class="admin-table">
    <thead>
      <tr>
        <th>S.N.</th>
        <th>Tour Name</th>
        <th>Name</th>
        <th>Email</th>
        <th>Phone</th>
        <th>Message</th>
        <th>Received Date</th>
        <th>Action</th>
      </tr>
    </thead>

    <tbody>
      <?php
      $i = 1;
      $result = mysqli_query($conn, "SELECT * FROM inquiries ORDER BY id DESC");
      while ($row = mysqli_fetch_assoc($result)) {
      ?>
        <tr>
          <td><?= $i++ ?></td>
          <td><?= htmlspecialchars($row['tour_name']) ?></td>
          <td><?= htmlspecialchars($row['name']) ?></td>
          <td><?= htmlspecialchars($row['email']) ?></td>
          <td><?= htmlspecialchars($row['phone']) ?></td>
          <td class="msg-cell"><?= nl2br(htmlspecialchars($row['message'])) ?></td>
          <td><?= htmlspecialchars($row['created_at']) ?></td>
          <td>
            <a href="?delete=<?= $row['id'] ?>"
              class="btn-delete"
              onclick="return confirm('Delete this inquiry?')">
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