<?php
include '../config/db.php';
include 'auth.php';
include '../includes/blog-functions.php';

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function catSlugify($name) {
    return strtolower(trim(preg_replace('/[^a-z0-9]+/', '-', strtolower($name)), '-'));
}

/* ADD */
if (isset($_POST['submit'])) {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("CSRF validation failed.");
    }

    $name = trim($_POST['name']);

    if ($name === '') {
        $_SESSION['error'] = "Category name is required.";
        $_SESSION['reopen_modal'] = 'addModal';
    } else {
        $slug = catSlugify($name);
        $stmt = $conn->prepare("INSERT INTO blog_categories (name, slug) VALUES (?, ?)");
        $stmt->bind_param("ss", $name, $slug);
        if ($stmt->execute()) {
            $_SESSION['success'] = "Category added successfully.";
        } else {
            $_SESSION['error'] = "Category already exists or could not be added.";
        }
        $stmt->close();
    }

    header("Location: manage-blog-categories" . (!empty($_POST['qs']) ? '?' . $_POST['qs'] : ''));
    exit();
}

/* UPDATE */
if (isset($_POST['update'])) {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("CSRF validation failed.");
    }

    $id = (int)$_POST['id'];
    $name = trim($_POST['name']);

    if ($id <= 0 || $name === '') {
        $_SESSION['error'] = "Category name is required.";
        $_SESSION['reopen_modal'] = 'editModal';
    } else {
        $slug = catSlugify($name);
        $stmt = $conn->prepare("UPDATE blog_categories SET name=?, slug=? WHERE id=?");
        $stmt->bind_param("ssi", $name, $slug, $id);
        if ($stmt->execute()) {
            $_SESSION['success'] = "Category updated successfully.";
        } else {
            $_SESSION['error'] = "Category name already exists or could not be updated.";
        }
        $stmt->close();
    }

    header("Location: manage-blog-categories" . (!empty($_POST['qs']) ? '?' . $_POST['qs'] : ''));
    exit();
}

/* DELETE */
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM blog_categories WHERE id=$id");
    $_SESSION['success'] = "Category deleted. Posts in it are now uncategorized.";
    header("Location: manage-blog-categories" . (isset($_GET['qs']) ? '?' . $_GET['qs'] : ''));
    exit();
}

/* SEARCH + PAGINATION */
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

$where = [];
$types = '';
$params = [];
if ($search !== '') {
    $where[] = "(c.name LIKE ? OR c.slug LIKE ?)";
    $like = "%$search%";
    $types .= 'ss';
    $params[] = $like; $params[] = $like;
}
$whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

$limit = 10;
$page = isset($_GET['page']) ? max((int)$_GET['page'], 1) : 1;
$offset = ($page - 1) * $limit;

$countStmt = $conn->prepare("SELECT COUNT(*) AS total FROM blog_categories c $whereSql");
if ($types) $countStmt->bind_param($types, ...$params);
$countStmt->execute();
$totalRows = $countStmt->get_result()->fetch_assoc()['total'];
$countStmt->close();
$totalPages = max((int)ceil($totalRows / $limit), 1);

$dataStmt = $conn->prepare("
    SELECT c.*, (SELECT COUNT(*) FROM blogs b WHERE b.category_id = c.id) AS post_count
    FROM blog_categories c
    $whereSql
    ORDER BY c.name ASC
    LIMIT ? OFFSET ?
");
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
  <h2>Blog Categories</h2>

  <?php include 'includes/admin-alert.php'; ?>

  <div class="crud-toolbar">
    <form class="toolbar-filters" method="GET">
      <div class="search-input">
        <input type="text" name="search" placeholder="Search category name..." value="<?= htmlspecialchars($search) ?>">
      </div>
      <button type="submit" class="btn-filter"><i class="fa-solid fa-filter"></i> Search</button>
      <?php if ($search !== ''): ?>
        <a href="manage-blog-categories" class="btn-reset">Reset</a>
      <?php endif; ?>
    </form>

    <button type="button" class="btn-add" onclick="openAddModal()"><i class="fa-solid fa-plus"></i> Add Category</button>
  </div>

  <p class="result-count"><?= $totalRows ?> categor<?= $totalRows == 1 ? 'y' : 'ies' ?> found</p>

  <div class="table-scroll"><table class="admin-table">
    <thead>
      <tr>
        <th>S.N.</th>
        <th>Name</th>
        <th>Slug</th>
        <th>Posts</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php $i = $offset + 1; ?>
      <?php if ($totalRows == 0): ?>
        <tr><td colspan="5" class="no-results">No categories match your search.</td></tr>
      <?php endif; ?>
      <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
          <td><?= $i++ ?></td>
          <td><?= htmlspecialchars($row['name']) ?></td>
          <td><?= htmlspecialchars($row['slug']) ?></td>
          <td><?= (int)$row['post_count'] ?></td>
          <td class="action-col">
            <button type="button" class="btn-edit"
              onclick="openEditModal(this)"
              data-id="<?= $row['id'] ?>"
              data-name="<?= htmlspecialchars($row['name']) ?>">
              Edit
            </button>
            <a href="javascript:void(0)"
              onclick="showConfirm('?delete=<?= $row['id'] ?>&qs=<?= urlencode($qs) ?>','Delete this category?')"
              class="btn-delete">
              Delete
            </a>
          </td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table></div>

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
    <h2>Add Category</h2>
    <form method="POST" class="admin-form">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
      <input type="hidden" name="qs" value="<?= htmlspecialchars($qs) ?>">
      <div class="form-group">
        <input type="text" name="name" placeholder="New category name" required>
      </div>
      <button name="submit">Add Category</button>
    </form>
  </div>
</div>

<!-- EDIT MODAL -->
<div class="crud-modal-overlay" id="editModal">
  <div class="crud-modal">
    <button type="button" class="modal-close" onclick="closeModal('editModal')">&times;</button>
    <h2>Edit Category</h2>
    <form method="POST" class="admin-form">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
      <input type="hidden" name="qs" value="<?= htmlspecialchars($qs) ?>">
      <input type="hidden" name="id" value="">
      <div class="form-group">
        <input type="text" name="name" placeholder="Category name" required>
      </div>
      <button name="update">Update Category</button>
    </form>
  </div>
</div>

<script src="assets/js/admin-alert.js"></script>
<script src="assets/js/admin-crud.js"></script>
<?php include 'includes/footer.php'; ?>
<script src="../assets/js/confirmation.js"></script>
