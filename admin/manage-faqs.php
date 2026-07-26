<?php
include '../config/db.php';
include 'auth.php';

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

/* ---------------------------------------------------------
   DELETE
--------------------------------------------------------- */
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if (mysqli_query($conn, "DELETE FROM faqs WHERE id=$id")) {
        $_SESSION['success'] = "FAQ deleted successfully.";
    } else {
        $_SESSION['error'] = "Failed to delete FAQ.";
    }
    header("Location: manage-faqs" . (isset($_GET['qs']) ? '?' . $_GET['qs'] : ''));
    exit();
}

/* ---------------------------------------------------------
   ADD
--------------------------------------------------------- */
if (isset($_POST['submit'])) {

    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("CSRF validation failed.");
    }

    $q = trim($_POST['question']);
    $a = trim($_POST['answer']);
    $f = (int)$_POST['featured'];
    $s = (int)$_POST['status'];

    if ($q === '' || $a === '') {
        $_SESSION['error'] = "Question and answer are required.";
        $_SESSION['reopen_modal'] = 'addModal';
    } else {
        $stmt = mysqli_prepare($conn, "INSERT INTO faqs (question, answer, is_featured, status) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "ssii", $q, $a, $f, $s);
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['success'] = "FAQ added successfully.";
        } else {
            $_SESSION['error'] = "Failed to add FAQ.";
        }
        mysqli_stmt_close($stmt);
    }

    header("Location: manage-faqs" . (!empty($_POST['qs']) ? '?' . $_POST['qs'] : ''));
    exit();
}

/* ---------------------------------------------------------
   UPDATE
--------------------------------------------------------- */
if (isset($_POST['update'])) {

    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("CSRF validation failed.");
    }

    $id = (int)$_POST['id'];
    $q = trim($_POST['question']);
    $a = trim($_POST['answer']);
    $f = (int)$_POST['is_featured'];
    $s = (int)$_POST['status'];

    if ($id <= 0 || $q === '' || $a === '') {
        $_SESSION['error'] = "Question and answer are required.";
        $_SESSION['reopen_modal'] = 'editModal';
    } else {
        $stmt = $conn->prepare("UPDATE faqs SET question=?, answer=?, is_featured=?, status=? WHERE id=?");
        $stmt->bind_param("ssiii", $q, $a, $f, $s, $id);
        if ($stmt->execute()) {
            $_SESSION['success'] = "FAQ updated successfully.";
        } else {
            $_SESSION['error'] = "Failed to update FAQ.";
        }
        $stmt->close();
    }

    header("Location: manage-faqs" . (!empty($_POST['qs']) ? '?' . $_POST['qs'] : ''));
    exit();
}

/* ---------------------------------------------------------
   SEARCH + FILTER + PAGINATION
--------------------------------------------------------- */
$search   = isset($_GET['search']) ? trim($_GET['search']) : '';
$status   = isset($_GET['status']) && $_GET['status'] !== '' ? (int)$_GET['status'] : null;
$featured = isset($_GET['featured']) && $_GET['featured'] !== '' ? (int)$_GET['featured'] : null;

$where  = [];
$types  = '';
$params = [];

if ($search !== '') {
    $where[] = "(question LIKE ? OR answer LIKE ?)";
    $like = "%$search%";
    $types .= 'ss';
    $params[] = $like;
    $params[] = $like;
}
if ($status !== null) {
    $where[] = "status = ?";
    $types .= 'i';
    $params[] = $status;
}
if ($featured !== null) {
    $where[] = "is_featured = ?";
    $types .= 'i';
    $params[] = $featured;
}

$whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

$limit = 10;
$page  = isset($_GET['page']) ? max((int)$_GET['page'], 1) : 1;
$offset = ($page - 1) * $limit;

// Total count (respecting filters)
$countSql = "SELECT COUNT(*) AS total FROM faqs $whereSql";
$countStmt = $conn->prepare($countSql);
if ($types) $countStmt->bind_param($types, ...$params);
$countStmt->execute();
$totalRows = $countStmt->get_result()->fetch_assoc()['total'];
$countStmt->close();
$totalPages = max((int)ceil($totalRows / $limit), 1);

// Data query
$dataSql = "SELECT * FROM faqs $whereSql ORDER BY id DESC LIMIT ? OFFSET ?";
$dataStmt = $conn->prepare($dataSql);
$allTypes = $types . 'ii';
$allParams = array_merge($params, [$limit, $offset]);
$dataStmt->bind_param($allTypes, ...$allParams);
$dataStmt->execute();
$result = $dataStmt->get_result();

// Preserve current filters across pagination / form actions
$qsArray = array_filter([
    'search'   => $search,
    'status'   => $status !== null ? $status : '',
    'featured' => $featured !== null ? $featured : '',
], fn($v) => $v !== '');
$qs = http_build_query($qsArray);

include 'includes/header.php';
include 'includes/sidebar.php';

$reopenModal = $_SESSION['reopen_modal'] ?? '';
unset($_SESSION['reopen_modal']);
?>
<link rel="stylesheet" href="assets/css/admin-crud.css">

<div class="admin-content" data-reopen-modal="<?= htmlspecialchars($reopenModal) ?>">
  <h2>FAQs</h2>

  <?php include 'includes/admin-alert.php'; ?>

  <div class="crud-toolbar">
    <form class="toolbar-filters" method="GET">
      <div class="search-input">
        <input type="text" name="search" placeholder="Search question or answer..." value="<?= htmlspecialchars($search) ?>">
      </div>

      <select name="status" class="auto-submit">
        <option value="">All Status</option>
        <option value="1" <?= $status === 1 ? 'selected' : '' ?>>Active</option>
        <option value="0" <?= $status === 0 ? 'selected' : '' ?>>Inactive</option>
      </select>

      <select name="featured" class="auto-submit">
        <option value="">Featured?</option>
        <option value="1" <?= $featured === 1 ? 'selected' : '' ?>>Yes</option>
        <option value="0" <?= $featured === 0 ? 'selected' : '' ?>>No</option>
      </select>

      <button type="submit" class="btn-filter"><i class="fa-solid fa-filter"></i> Filter</button>
      <?php if ($search !== '' || $status !== null || $featured !== null): ?>
        <a href="manage-faqs" class="btn-reset">Reset</a>
      <?php endif; ?>
    </form>

    <button type="button" class="btn-add" onclick="openAddModal()"><i class="fa-solid fa-plus"></i> Add FAQ</button>
  </div>

  <p class="result-count"><?= $totalRows ?> FAQ<?= $totalRows == 1 ? '' : 's' ?> found</p>

  <div class="table-scroll"><table class="admin-table">
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
      <?php $i = $offset + 1; ?>
      <?php if ($totalRows == 0): ?>
        <tr><td colspan="7" class="no-results">No FAQs match your search/filter.</td></tr>
      <?php endif; ?>
      <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
          <td><?= $i++ ?></td>
          <td><?= htmlspecialchars($row['created_at']) ?></td>
          <td><?= htmlspecialchars($row['question']) ?></td>
          <td><?= implode(' ', array_slice(explode(' ', $row['answer']), 0, 5)) ?>...</td>
          <td class="status-col"><span class="pill <?= $row['is_featured'] ? 'published' : 'draft' ?>"><?= $row['is_featured'] ? 'Yes' : 'No' ?></span></td>
          <td class="status-col"><span class="pill <?= $row['status'] ? 'published' : 'draft' ?>"><?= $row['status'] ? 'Active' : 'Inactive' ?></span></td>
          <td class="action-col-flight">
            <button type="button" class="btn-edit"
              onclick="openEditModal(this)"
              data-id="<?= $row['id'] ?>"
              data-question="<?= htmlspecialchars($row['question']) ?>"
              data-answer="<?= htmlspecialchars($row['answer']) ?>"
              data-is_featured="<?= $row['is_featured'] ?>"
              data-status="<?= $row['status'] ?>">
              Edit
            </button>
            <a href="javascript:void(0)"
              onclick="showConfirm('?delete=<?= $row['id'] ?>&qs=<?= urlencode($qs) ?>','Delete this FAQ?')"
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
    <h2>Add FAQ</h2>
    <form method="POST" class="admin-form validate-form">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
      <input type="hidden" name="qs" value="<?= htmlspecialchars($qs) ?>">

      <div class="form-group">
        <input type="text" name="question" placeholder="Question" data-validate="text10">
        <small class="error"></small>
      </div>

      <div class="form-group">
        <textarea name="answer" placeholder="Answer" data-validate="text10"></textarea>
        <small class="error"></small>
      </div>

      <label>Featured</label>
      <select name="featured" required>
        <option value="0">No</option>
        <option value="1">Yes</option>
      </select>

      <label>Status</label>
      <select name="status" required>
        <option value="1">Active</option>
        <option value="0">Inactive</option>
      </select>

      <button type="submit" name="submit">Add FAQ</button>
    </form>
  </div>
</div>

<!-- EDIT MODAL -->
<div class="crud-modal-overlay" id="editModal">
  <div class="crud-modal">
    <button type="button" class="modal-close" onclick="closeModal('editModal')">&times;</button>
    <h2>Edit FAQ</h2>
    <form method="POST" class="admin-form validate-form">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
      <input type="hidden" name="qs" value="<?= htmlspecialchars($qs) ?>">
      <input type="hidden" name="id" value="">

      <div class="form-group">
        <input type="text" name="question" placeholder="Question" data-validate="text10">
        <small class="error"></small>
      </div>

      <div class="form-group">
        <textarea name="answer" placeholder="Answer" data-validate="text10"></textarea>
        <small class="error"></small>
      </div>

      <label>Featured</label>
      <select name="is_featured">
        <option value="0">No</option>
        <option value="1">Yes</option>
      </select>

      <label>Status</label>
      <select name="status">
        <option value="1">Active</option>
        <option value="0">Inactive</option>
      </select>

      <button type="submit" name="update">Update FAQ</button>
    </form>
  </div>
</div>

<script src="assets/js/form-validator.js"></script>
<script src="assets/js/admin-alert.js"></script>
<script src="assets/js/admin-crud.js"></script>

<?php include 'includes/footer.php'; ?>
<script src="../assets/js/confirmation.js"></script>
