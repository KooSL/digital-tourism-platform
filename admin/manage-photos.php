<?php
include '../config/db.php';
include 'auth.php';

if (empty($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$albumId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$album = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM gallery_albums WHERE id=$albumId"));

if (!$album) {
  header("Location: manage-albums");
  exit();
}
$slug = $album['slug'];

/* DELETE */
if (isset($_GET['delete'])) {
  $photoId = (int)$_GET['delete'];
  if (mysqli_query($conn, "DELETE FROM gallery_photos WHERE id=$photoId")) {
    $_SESSION['success'] = "Photo deleted successfully.";
  } else {
    $_SESSION['error'] = "Failed to delete photo.";
  }
  header("Location: manage-photos?id=$albumId&slug=" . urlencode($slug) . (isset($_GET['qs']) ? '&' . $_GET['qs'] : ''));
  exit();
}

/* UPLOAD (ADD) */
if (isset($_POST['upload'])) {
  if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    die("CSRF validation failed.");
  }

  $path = "../uploads/gallery/" . $slug . "/";
  if (!is_dir($path)) mkdir($path, 0755, true);

  if (empty($_FILES['images']['name'][0])) {
    $_SESSION['error'] = "Please choose at least one photo.";
    $_SESSION['reopen_modal'] = 'addModal';
  } else {
    $stmt = $conn->prepare("INSERT INTO gallery_photos (album_id, image) VALUES (?, ?)");
    $uploaded = 0;
    foreach ($_FILES['images']['name'] as $key => $img) {
      $safe_name = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', basename($img));
      if (move_uploaded_file($_FILES['images']['tmp_name'][$key], $path . $safe_name)) {
        $stmt->bind_param("is", $albumId, $safe_name);
        $stmt->execute();
        $uploaded++;
      }
    }
    $stmt->close();
    $_SESSION['success'] = "$uploaded photo(s) uploaded successfully.";
  }

  header("Location: manage-photos?id=$albumId&slug=" . urlencode($slug) . (!empty($_POST['qs']) ? '&' . $_POST['qs'] : ''));
  exit();
}

/* SEARCH + PAGINATION */
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

$where = ["album_id = ?"];
$types = 'i';
$params = [$albumId];

if ($search !== '') {
  $where[] = "image LIKE ?";
  $types .= 's';
  $params[] = "%$search%";
}
$whereSql = 'WHERE ' . implode(' AND ', $where);

$limit = 12;
$page = isset($_GET['page']) ? max((int)$_GET['page'], 1) : 1;
$offset = ($page - 1) * $limit;

$countStmt = $conn->prepare("SELECT COUNT(*) AS total FROM gallery_photos $whereSql");
$countStmt->bind_param($types, ...$params);
$countStmt->execute();
$totalRows = $countStmt->get_result()->fetch_assoc()['total'];
$countStmt->close();
$totalPages = max((int)ceil($totalRows / $limit), 1);

$dataStmt = $conn->prepare("SELECT * FROM gallery_photos $whereSql ORDER BY id DESC LIMIT ? OFFSET ?");
$allTypes = $types . 'ii';
$allParams = array_merge($params, [$limit, $offset]);
$dataStmt->bind_param($allTypes, ...$allParams);
$dataStmt->execute();
$result = $dataStmt->get_result();

$qsArray = array_filter(['search' => $search], fn($v) => $v !== '');
$qs = http_build_query($qsArray);

include 'includes/header.php';
include 'includes/sidebar.php';

$reopenModal = $_SESSION['reopen_modal'] ?? '';
unset($_SESSION['reopen_modal']);
?>
<link rel="stylesheet" href="assets/css/admin-crud.css">

<div class="admin-content" data-reopen-modal="<?= htmlspecialchars($reopenModal) ?>">
  <h2>Photos in "<?= htmlspecialchars($album['title']) ?>" <a href="manage-albums" class="btn-view" style="margin-left:10px;">&larr; Back to Albums</a></h2>

  <?php include 'includes/admin-alert.php'; ?>

  <div class="crud-toolbar">
    <form class="toolbar-filters" method="GET">
      <input type="hidden" name="id" value="<?= $albumId ?>">
      <input type="hidden" name="slug" value="<?= htmlspecialchars($slug) ?>">
      <div class="search-input">
        <input type="text" name="search" placeholder="Search photo filename..." value="<?= htmlspecialchars($search) ?>">
      </div>
      <button type="submit" class="btn-filter"><i class="fa-solid fa-filter"></i> Search</button>
      <?php if ($search !== ''): ?>
        <a href="manage-photos?id=<?= $albumId ?>&slug=<?= urlencode($slug) ?>" class="btn-reset">Reset</a>
      <?php endif; ?>
    </form>

    <button type="button" class="btn-add" onclick="openAddModal()"><i class="fa-solid fa-plus"></i> Add Photos</button>
  </div>

  <p class="result-count"><?= $totalRows ?> photo<?= $totalRows == 1 ? '' : 's' ?> found</p>

  <div class="table-scroll">
    <table class="admin-table">
      <thead>
        <tr>
          <th>S.N.</th>
          <th>Created Date</th>
          <th>Image</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php $i = $offset + 1; ?>
        <?php if ($totalRows == 0): ?>
          <tr>
            <td colspan="4" class="no-results">No photos in this album yet.</td>
          </tr>
        <?php endif; ?>
        <?php while ($row = $result->fetch_assoc()): ?>
          <tr>
            <td><?= $i++ ?></td>
            <td><?= htmlspecialchars($row['created_at']) ?></td>
            <td><img src="../uploads/gallery/<?= htmlspecialchars($slug) ?>/<?= htmlspecialchars($row['image']) ?>" height="50"></td>
            <td class="action-col-flight">
              <a href="javascript:void(0)"
                onclick="showConfirm('?delete=<?= $row['id'] ?>&id=<?= $albumId ?>&slug=<?= urlencode($slug) ?>&qs=<?= urlencode($qs) ?>','Delete this photo?')"
                class="btn-delete">
                Delete
              </a>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>

  <?php if ($totalPages > 1): ?>
    <div class="pagination">
      <?php for ($p = 1; $p <= $totalPages; $p++): ?>
        <a href="?<?= http_build_query(array_merge(['id' => $albumId, 'slug' => $slug], $qsArray, ['page' => $p])) ?>" class="page-btn <?= $p == $page ? 'active' : '' ?>"><?= $p ?></a>
      <?php endfor; ?>
    </div>
  <?php endif; ?>
</div>

<!-- ADD MODAL -->
<div class="crud-modal-overlay" id="addModal">
  <div class="crud-modal">
    <button type="button" class="modal-close" onclick="closeModal('addModal')">&times;</button>
    <h2>Add Photos to "<?= htmlspecialchars($album['title']) ?>"</h2>
    <form method="POST" enctype="multipart/form-data" class="admin-form">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
      <input type="hidden" name="qs" value="<?= htmlspecialchars($qs) ?>">

      <div class="file_input">
        <label>Select Photos</label>
        <input type="file" name="images[]" multiple accept="image/*" required>
      </div>

      <button type="submit" name="upload">Upload</button>
    </form>
  </div>
</div>

<script src="assets/js/admin-alert.js"></script>
<script src="assets/js/admin-crud.js"></script>

<?php include 'includes/footer.php'; ?>
<script src="../assets/js/confirmation.js"></script>