<?php
include '../config/db.php';
include 'auth.php';
include 'includes/header.php';
include 'includes/sidebar.php';


// DELETE TOUR
if (isset($_GET['delete'])) {
  $id = $_GET['delete'];

  // $query = mysqli_query($conn, "SELECT image, pdf FROM tours WHERE id=$id");
  // $data = mysqli_fetch_assoc($query);

  // if($data){
  //   @unlink("assets/images/".$data['banner_image']);
  //   @unlink("assets/pdf/".$data['pdf_file']);
  // }

  if (mysqli_query($conn, "DELETE FROM tours WHERE id=$id")) {
    $_SESSION['success'] = "Tour deleted successfully.";
  } else {
    $_SESSION['error'] = "Failed to delete tour.";
  }
  header("Location: manage-tours");
  exit;
}
?>

<div class="admin-content">
  <h2>Manage Tours</h2>

  <?php include 'includes/admin-alert.php'; ?>

  <table class="admin-table">
    <thead>
      <tr>
        <th>S.N.</th>
        <th>created Date</th>
        <th>Title</th>
        <th>Type</th>
        <th>Duration</th>
        <th>Price</th>
        <th>Price USD</th>
        <th>Overview</th>
        <th>Highlights</th>
        <th>Itinerary</th>
        <th>Includes</th>
        <th>Excludes</th>
        <th>Image</th>
        <th>PDF</th>
        <th>Popular</th>
        <th>Status</th>
        <th>Action</th>


      </tr>
    </thead>

    <tbody>
      <?php
      $i = 1;
      $result = mysqli_query($conn, "SELECT * FROM tours ORDER BY id DESC");
      while ($row = mysqli_fetch_assoc($result)) {
        $itRes = mysqli_query(
          $conn,
          "SELECT day_number, title 
          FROM tour_itineraries 
          WHERE tour_id = {$row['id']} 
          ORDER BY day_number ASC 
          LIMIT 1"
        );
      ?>
        <tr>
          <td><?= $i++ ?></td>
          <td><?= $row['created_at'] ?></td>

          <td><?= $row['title'] ?></td>
          <td><?= $row['type'] ?></td>
          <td><?= $row['duration'] ?></td>
          <td><?= $row['price'] ?></td>
          <td><?= $row['price_usd'] ?></td>
          <td>
            <?= implode(' ', array_slice(explode(' ', $row['overview']), 0, 5)); ?>...
          </td>
          <td>
            <?= implode(' ', array_slice(explode(' ', $row['highlights']), 0, 5)); ?>...
          </td>

          <td>
            <?php if (mysqli_num_rows($itRes) > 0): ?>
              <ul style="padding-left:15px; margin:0;">
                <?php while ($it = mysqli_fetch_assoc($itRes)): ?>
                  <li>
                    <strong>Day <?= $it['day_number'] ?>:</strong>
                    <?= htmlspecialchars($it['title']) ?>
                  </li>
                <?php endwhile; ?>
              </ul>
              <small style="color:#777;">+ more</small>
            <?php else: ?>
              <em>No itinerary</em>
            <?php endif; ?>
          </td>

          <td>
            <?= implode(' ', array_slice(explode(' ', $row['includes']), 0, 5)); ?>...
          </td>

          <td>
            <?= implode(' ', array_slice(explode(' ', $row['excludes']), 0, 5)); ?>...
          </td>

          <td>
            <img src="uploads/images/tours/<?= $row['banner_image'] ?>" height="50">
          </td>

          <td>
            <div class="pdf-view">
              <a href="uploads/pdf/<?= $row['pdf_file'] ?>" target="_blank">View</a>
            </div>

          </td>

          <td>
            <?= $row['is_popular'] ? '<span class="badge badge-popular">Yes</span>' : 'No'; ?>
          </td>

          <?php
          if ($row['status'] == 1) {
            echo '<td class="status-col published">Active</td>';
          } else {
            echo '<td class="status-col draft">Inactive</td>';
          }
          ?>

          <td class="action-col">
            <a href="edit-tour?id=<?= $row['id'] ?>" class="btn-edit">Edit</a>
            <a href="?delete=<?= $row['id'] ?>"
              onclick="return confirm('Delete this tour?')"
              class="btn-delete">
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