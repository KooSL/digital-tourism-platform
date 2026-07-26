<?php
include '../config/db.php';
include 'auth.php';

if (empty($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function slugify($title)
{
  return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title), '-'));
}

/* DELETE */
if (isset($_GET['delete'])) {
  $id = (int)$_GET['delete'];
  if (mysqli_query($conn, "DELETE FROM gallery_albums WHERE id=$id")) {
    $_SESSION['success'] = "Album deleted successfully.";
  } else {
    $_SESSION['error'] = "Failed to delete album.";
  }
  header("Location: manage-albums" . (isset($_GET['qs']) ? '?' . $_GET['qs'] : ''));
  exit();
}

/* ADD */
if (isset($_POST['create_album'])) {
  if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    die("CSRF validation failed.");
  }

  $title = trim($_POST['title']);
  $slug = slugify($title);

  if ($title === '' || empty($_FILES['cover']['name'])) {
    $_SESSION['error'] = "Title and cover image are required.";
    $_SESSION['reopen_modal'] = 'addModal';
  } else {
    $folder = "../uploads/gallery/$slug";
    if (!is_dir($folder)) mkdir($folder, 0777, true);

    $cover = uniqid() . '_' . basename($_FILES['cover']['name']);
    move_uploaded_file($_FILES['cover']['tmp_name'], "$folder/$cover");

    $stmt = $conn->prepare("INSERT INTO gallery_albums (title, slug, cover_image, status) VALUES (?, ?, ?, 1)");
    $stmt->bind_param("sss", $title, $slug, $cover);
    if ($stmt->execute()) {
      $_SESSION['success'] = "Album created successfully.";
    } else {
      $_SESSION['error'] = "Failed to create album.";
    }
    $stmt->close();
  }

  header("Location: manage-albums" . (!empty($_POST['qs']) ? '?' . $_POST['qs'] : ''));
  exit();
}

/* UPDATE */
if (isset($_POST['update'])) {
  if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    die("CSRF validation failed.");
  }

  $id = (int)$_POST['id'];
  $existing = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM gallery_albums WHERE id=$id"));

  if ($id <= 0 || !$existing) {
    $_SESSION['error'] = "Invalid album.";
    $_SESSION['reopen_modal'] = 'editModal';
    header("Location: manage-albums" . (!empty($_POST['qs']) ? '?' . $_POST['qs'] : ''));
    exit();
  }

  $title = trim($_POST['title']);
  $status = (int)$_POST['status'];

  if ($title === '') {
    $_SESSION['error'] = "Title is required.";
    $_SESSION['reopen_modal'] = 'editModal';
  } else {
    $oldSlug = $existing['slug'];
    $newSlug = slugify($title);
    $cover = $existing['cover_image'];

    if ($oldSlug !== $newSlug) {
      $oldPath = "../uploads/gallery/" . $oldSlug;
      $newPath = "../uploads/gallery/" . $newSlug;
      if (is_dir($oldPath) && !is_dir($newPath)) {
        rename($oldPath, $newPath);
      }
    }

    if (!empty($_FILES['cover']['name'])) {
      $newCover = uniqid() . '_' . basename($_FILES['cover']['name']);
      $folder = "../uploads/gallery/$newSlug";
      if (!is_dir($folder)) mkdir($folder, 0777, true);
      if (move_uploaded_file($_FILES['cover']['tmp_name'], "$folder/$newCover")) {
        @unlink("$folder/$cover");
        $cover = $newCover;
      }
    }

    $stmt = $conn->prepare("UPDATE gallery_albums SET title=?, slug=?, cover_image=?, status=? WHERE id=?");
    $stmt->bind_param("sssii", $title, $newSlug, $cover, $status, $id);
    if ($stmt->execute()) {
      $_SESSION['success'] = "Album updated successfully.";
    } else {
      $_SESSION['error'] = "Failed to update album.";
    }
    $stmt->close();
  }

  header("Location: manage-albums" . (!empty($_POST['qs']) ? '?' . $_POST['qs'] : ''));
  exit();
}

/* SEARCH + PAGINATION */
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

$where = [];
$types = '';
$params = [];

if ($search !== '') {
  $where[] = "(title LIKE ? OR slug LIKE ?)";
  $like = "%$search%";
  $types .= 'ss';
  $params[] = $like;
  $params[] = $like;
}

$whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

$limit = 10;
$page = isset($_GET['page']) ? max((int)$_GET['page'], 1) : 1;
$offset = ($page - 1) * $limit;

$countStmt = $conn->prepare("SELECT COUNT(*) AS total FROM gallery_albums $whereSql");
if ($types) $countStmt->bind_param($types, ...$params);
$countStmt->execute();
$totalRows = $countStmt->get_result()->fetch_assoc()['total'];
$countStmt->close();
$totalPages = max((int)ceil($totalRows / $limit), 1);

$dataStmt = $conn->prepare("SELECT * FROM gallery_albums $whereSql ORDER BY id DESC LIMIT ? OFFSET ?");
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
  <h2>Gallery Albums</h2>

  <?php include 'includes/admin-alert.php'; ?>

  <div class="crud-toolbar">
    <form class="toolbar-filters" method="GET">
      <div class="search-input">
        <input type="text" name="search" placeholder="Search album title..." value="<?= htmlspecialchars($search) ?>">
      </div>
      <button type="submit" class="btn-filter"><i class="fa-solid fa-filter"></i> Search</button>
      <?php if ($search !== ''): ?>
        <a href="manage-albums" class="btn-reset">Reset</a>
      <?php endif; ?>
    </form>

    <button type="button" class="btn-add" onclick="openAddModal()"><i class="fa-solid fa-plus"></i> Add Album</button>
  </div>

  <p class="result-count"><?= $totalRows ?> album<?= $totalRows == 1 ? '' : 's' ?> found</p>

  <div class="table-scroll">
    <table class="admin-table">
      <thead>
        <tr>
          <th>S.N.</th>
          <th>Created Date</th>
          <th>Title</th>
          <th>Slug</th>
          <th>Cover Image</th>
          <th>Status</th>
          <th>Photos</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php $i = $offset + 1; ?>
        <?php if ($totalRows == 0): ?>
          <tr>
            <td colspan="8" class="no-results">No albums match your search.</td>
          </tr>
        <?php endif; ?>
        <?php while ($row = $result->fetch_assoc()):
          $photoCount = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM gallery_photos WHERE album_id = {$row['id']}"))['total'];
        ?>
          <tr>
            <td><?= $i++ ?></td>
            <td><?= htmlspecialchars($row['created_at']) ?></td>
            <td><?= htmlspecialchars($row['title']) ?></td>
            <td><?= htmlspecialchars($row['slug']) ?></td>
            <td><img src="../uploads/gallery/<?= htmlspecialchars($row['slug']) ?>/<?= htmlspecialchars($row['cover_image']) ?>" height="50"></td>
            <td class="status-col"><span class="pill <?= $row['status'] ? 'published' : 'draft' ?>"><?= $row['status'] ? 'Active' : 'Inactive' ?></span></td>
            <td class="action-col-flight">
              <p><?= $photoCount ?> Photos</p>
              <a href="manage-photos?id=<?= $row['id'] ?>&slug=<?= urlencode($row['slug']) ?>" class="btn-view">View / Add Photos</a>
            </td>
            <td class="action-col-flight">
              <button type="button" class="btn-edit"
                onclick="openEditModal(this)"
                data-id="<?= $row['id'] ?>"
                data-title="<?= htmlspecialchars($row['title']) ?>"
                data-status="<?= $row['status'] ?>"
                data-image="<?= htmlspecialchars($row['cover_image']) ?>"
                data-image-path="../uploads/gallery/<?= htmlspecialchars($row['slug']) ?>/<?= htmlspecialchars($row['cover_image']) ?>">
                Edit
              </button>
              <a href="javascript:void(0)"
                onclick="showConfirm('?delete=<?= $row['id'] ?>&qs=<?= urlencode($qs) ?>','Delete this album and all its photos?')"
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
        <a href="?<?= http_build_query(array_merge($qsArray, ['page' => $p])) ?>" class="page-btn <?= $p == $page ? 'active' : '' ?>"><?= $p ?></a>
      <?php endfor; ?>
    </div>
  <?php endif; ?>
</div>

<!-- ADD MODAL -->
<div class="crud-modal-overlay" id="addModal">
  <div class="crud-modal">
    <button type="button" class="modal-close" onclick="closeModal('addModal')">&times;</button>
    <h2>Add Album</h2>
    <form method="POST" enctype="multipart/form-data" class="admin-form validate-form">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
      <input type="hidden" name="qs" value="<?= htmlspecialchars($qs) ?>">

      <div class="form-group">
        <input type="text" name="title" placeholder="Album Title" data-validate="name">
        <small class="error"></small>
      </div>

      <div class="file_input">
        <label>Cover Image</label>
        <input type="file" name="cover" accept="image/*" required>
      </div>

      <button type="submit" name="create_album">Create Album</button>
    </form>
  </div>
</div>

<!-- EDIT MODAL -->
<div class="crud-modal-overlay" id="editModal">
  <div class="crud-modal">
    <button type="button" class="modal-close" onclick="closeModal('editModal')">&times;</button>
    <h2>Edit Album</h2>
    <form method="POST" enctype="multipart/form-data" class="admin-form validate-form">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
      <input type="hidden" name="qs" value="<?= htmlspecialchars($qs) ?>">
      <input type="hidden" name="id" value="">

      <div class="form-group">
        <input type="text" name="title" placeholder="Album Title" data-validate="name">
        <small class="error"></small>
      </div>

      <label>Current Cover</label>
      <div class="current-image">
        <img src="" width="120" alt="">
      </div>

      <div class="file_input">
        <label>Change Cover</label>
        <input type="file" name="cover" accept="image/*">
      </div>

      <label>Status</label>
      <select name="status">
        <option value="1">Active</option>
        <option value="0">Inactive</option>
      </select>

      <button type="submit" name="update">Update Album</button>
    </form>
  </div>
</div>

<script src="assets/js/form-validator.js"></script>
<script src="assets/js/admin-alert.js"></script>
<script src="assets/js/admin-crud.js"></script>

<?php include 'includes/footer.php'; ?>
<script src="../assets/js/confirmation.js"></script>