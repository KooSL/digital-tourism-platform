<?php
include '../config/db.php';
include 'auth.php';
include 'includes/header.php';
include 'includes/sidebar.php';


if(isset($_GET['delete'])){
  $id = $_GET['delete'];
  mysqli_query($conn, "DELETE FROM faqs WHERE id=$id");
  header("Location: manage-faqs");
}
?>

<div class="admin-content">
  <h2>Manage FAQs</h2>

  <table class="admin-table">
    <thead>
      <tr>
        <th>S.N.</th>
        <th>Created Date</th>
        <th>Question</th>
        <th>Answer</th>
        <th>Is Featured</th>
        <th>Status</th>
        <th>Action</th>

      </tr>
    </thead>

    <tbody>
      <?php
      $i = 1;
      $result = mysqli_query($conn, "SELECT * FROM faqs ORDER BY id DESC");
      while($row = mysqli_fetch_assoc($result)){
      ?>
      <tr>
        <td><?= $i++ ?></td>
        <td><?= $row['created_at'] ?></td>
        <td><?= $row['question'] ?></td>
        <td>
          <?= implode(' ', array_slice(explode(' ', $row['answer']), 0, 5)); ?>...
        </td>
        <?php
          if($row['is_featured'] == 1){
            echo '<td class="status-col published">Yes</td>';
          } else {
            echo '<td class="status-col draft">No</td>';
          }
        ?>
        
        <?php
          if($row['status'] == 1){
            echo '<td class="status-col published">Active</td>';
          } else {
            echo '<td class="status-col draft">Inactive</td>';
          }
        ?>

        <td class="action-col-flight">
          <a href="edit-faq?id=<?= $row['id'] ?>" class="btn-edit">Edit</a>
          <a href="?delete=<?= $row['id'] ?>"
            onclick="return confirm('Delete this FAQ?')"
            class="btn-delete">
            Delete
          </a>
        </td>

      </tr>
      <?php } ?>
    </tbody>
  </table>
</div>

<?php include 'includes/footer.php'; ?>

