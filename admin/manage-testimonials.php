<?php
include '../config/db.php';
include 'auth.php';

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

/* DELETE */
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if (mysqli_query($conn, "DELETE FROM testimonials WHERE id=$id")) {
        $_SESSION['success'] = "Testimonial deleted successfully.";
    } else {
        $_SESSION['error'] = "Failed to delete testimonial.";
    }
    header("Location: manage-testimonials" . (isset($_GET['qs']) ? '?' . $_GET['qs'] : ''));
    exit();
}

/* ADD */
if (isset($_POST['submit'])) {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("CSRF validation failed.");
    }

    $name    = trim($_POST['name']);
    $service = trim($_POST['service']);
    $review  = trim($_POST['review']);
    $rating  = (int)$_POST['rating'];
    $status  = (int)$_POST['status'];

    if ($name === '' || $service === '' || $review === '') {
        $_SESSION['error'] = "Name, service and review are required.";
        $_SESSION['reopen_modal'] = 'addModal';
    } else {
        $stmt = $conn->prepare("INSERT INTO testimonials (name, service, review, rating, status) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssii", $name, $service, $review, $rating, $status);
        if ($stmt->execute()) {
            $_SESSION['success'] = "Testimonial added successfully.";
        } else {
            $_SESSION['error'] = "Failed to add testimonial.";
        }
        $stmt->close();
    }

    header("Location: manage-testimonials" . (!empty($_POST['qs']) ? '?' . $_POST['qs'] : ''));
    exit();
}

/* UPDATE */
if (isset($_POST['update'])) {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("CSRF validation failed.");
    }

    $id      = (int)$_POST['id'];
    $name    = trim($_POST['name']);
    $service = trim($_POST['service']);
    $review  = trim($_POST['review']);
    $rating  = (int)$_POST['rating'];
    $status  = (int)$_POST['status'];

    if ($id <= 0 || $name === '' || $service === '' || $review === '') {
        $_SESSION['error'] = "Name, service and review are required.";
        $_SESSION['reopen_modal'] = 'editModal';
    } else {
        $stmt = $conn->prepare("UPDATE testimonials SET name=?, service=?, review=?, rating=?, status=? WHERE id=?");
        $stmt->bind_param("sssiii", $name, $service, $review, $rating, $status, $id);
        if ($stmt->execute()) {
            $_SESSION['success'] = "Testimonial updated successfully.";
        } else {
            $_SESSION['error'] = "Failed to update testimonial.";
        }
        $stmt->close();
    }

    header("Location: manage-testimonials" . (!empty($_POST['qs']) ? '?' . $_POST['qs'] : ''));
    exit();
}

/* SEARCH + FILTER + PAGINATION */
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$status = isset($_GET['status']) && $_GET['status'] !== '' ? (int)$_GET['status'] : null;
$rating = isset($_GET['rating']) && $_GET['rating'] !== '' ? (int)$_GET['rating'] : null;

$where = [];
$types = '';
$params = [];

if ($search !== '') {
    $where[] = "(name LIKE ? OR service LIKE ? OR review LIKE ?)";
    $like = "%$search%";
    $types .= 'sss';
    $params[] = $like; $params[] = $like; $params[] = $like;
}
if ($status !== null) {
    $where[] = "status = ?";
    $types .= 'i';
    $params[] = $status;
}
if ($rating !== null) {
    $where[] = "rating = ?";
    $types .= 'i';
    $params[] = $rating;
}

$whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

$limit = 10;
$page = isset($_GET['page']) ? max((int)$_GET['page'], 1) : 1;
$offset = ($page - 1) * $limit;

$countStmt = $conn->prepare("SELECT COUNT(*) AS total FROM testimonials $whereSql");
if ($types) $countStmt->bind_param($types, ...$params);
$countStmt->execute();
$totalRows = $countStmt->get_result()->fetch_assoc()['total'];
$countStmt->close();
$totalPages = max((int)ceil($totalRows / $limit), 1);

$dataStmt = $conn->prepare("SELECT * FROM testimonials $whereSql ORDER BY id DESC LIMIT ? OFFSET ?");
$allTypes = $types . 'ii';
$allParams = array_merge($params, [$limit, $offset]);
$dataStmt->bind_param($allTypes, ...$allParams);
$dataStmt->execute();
$result = $dataStmt->get_result();

$qsArray = array_filter([
    'search' => $search,
    'status' => $status !== null ? $status : '',
    'rating' => $rating !== null ? $rating : '',
], fn($v) => $v !== '');
$qs = http_build_query($qsArray);

include 'includes/header.php';
include 'includes/sidebar.php';

$reopenModal = $_SESSION['reopen_modal'] ?? '';
unset($_SESSION['reopen_modal']);
?>
<link rel="stylesheet" href="assets/css/admin-crud.css">

<div class="admin-content" data-reopen-modal="<?= htmlspecialchars($reopenModal) ?>">
  <h2>Testimonials</h2>

  <?php include 'includes/admin-alert.php'; ?>

  <div class="crud-toolbar">
    <form class="toolbar-filters" method="GET">
      <div class="search-input">
        <input type="text" name="search" placeholder="Search name, service, review..." value="<?= htmlspecialchars($search) ?>">
      </div>

      <select name="rating" class="auto-submit">
        <option value="">All Ratings</option>
        <?php for ($r = 5; $r >= 1; $r--): ?>
          <option value="<?= $r ?>" <?= $rating === $r ? 'selected' : '' ?>><?= $r ?> ★</option>
        <?php endfor; ?>
      </select>

      <select name="status" class="auto-submit">
        <option value="">All Status</option>
        <option value="1" <?= $status === 1 ? 'selected' : '' ?>>Active</option>
        <option value="0" <?= $status === 0 ? 'selected' : '' ?>>Inactive</option>
      </select>

      <button type="submit" class="btn-filter"><i class="fa-solid fa-filter"></i> Filter</button>
      <?php if ($search !== '' || $status !== null || $rating !== null): ?>
        <a href="manage-testimonials" class="btn-reset">Reset</a>
      <?php endif; ?>
    </form>

    <button type="button" class="btn-add" onclick="openAddModal()"><i class="fa-solid fa-plus"></i> Add Testimonial</button>
  </div>

  <p class="result-count"><?= $totalRows ?> testimonial<?= $totalRows == 1 ? '' : 's' ?> found</p>

  <div class="table-scroll"><table class="admin-table">
    <thead>
      <tr>
        <th>S.N</th>
        <th>Name</th>
        <th>Service</th>
        <th>Review</th>
        <th>Rating</th>
        <th>Status</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php $i = $offset + 1; ?>
      <?php if ($totalRows == 0): ?>
        <tr><td colspan="7" class="no-results">No testimonials match your search/filter.</td></tr>
      <?php endif; ?>
      <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
          <td><?= $i++ ?></td>
          <td><?= htmlspecialchars($row['name']) ?></td>
          <td><?= htmlspecialchars($row['service']) ?></td>
          <td><?= implode(' ', array_slice(explode(' ', $row['review']), 0, 6)) ?>...</td>
          <td><?= $row['rating'] ?><span class="ratingstar"> ★</span></td>
          <td class="<?= $row['status'] ? 'published' : 'draft' ?>"><?= $row['status'] ? 'Active' : 'Inactive' ?></td>
          <td class="action-col">
            <button type="button" class="btn-edit"
              onclick="openEditModal(this)"
              data-id="<?= $row['id'] ?>"
              data-name="<?= htmlspecialchars($row['name']) ?>"
              data-service="<?= htmlspecialchars($row['service']) ?>"
              data-review="<?= htmlspecialchars($row['review']) ?>"
              data-rating="<?= $row['rating'] ?>"
              data-status="<?= $row['status'] ?>">
              Edit
            </button>
            <a href="javascript:void(0)"
              onclick="showConfirm('?delete=<?= $row['id'] ?>&qs=<?= urlencode($qs) ?>','Delete this testimonial?')"
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
    <h2>Add Testimonial</h2>
    <form method="POST" class="admin-form validate-form">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
      <input type="hidden" name="qs" value="<?= htmlspecialchars($qs) ?>">

      <div class="form-group">
        <input type="text" name="name" placeholder="Client Name" data-validate="name">
        <small class="error"></small>
      </div>
      <div class="form-group">
        <input type="text" name="service" placeholder="Service (Tour / Flight / Visa)" data-validate="name">
        <small class="error"></small>
      </div>
      <div class="form-group">
        <textarea name="review" placeholder="Client Review" data-validate="text10"></textarea>
        <small class="error"></small>
      </div>

      <label>Rating</label>
      <select name="rating" class="rating">
        <?php for ($r = 5; $r >= 1; $r--): ?>
          <option value="<?= $r ?>"><?= $r ?> ★</option>
        <?php endfor; ?>
      </select>

      <label>Status</label>
      <select name="status">
        <option value="1">Active</option>
        <option value="0">Inactive</option>
      </select>

      <button type="submit" name="submit">Add Testimonial</button>
    </form>
  </div>
</div>

<!-- EDIT MODAL -->
<div class="crud-modal-overlay" id="editModal">
  <div class="crud-modal">
    <button type="button" class="modal-close" onclick="closeModal('editModal')">&times;</button>
    <h2>Edit Testimonial</h2>
    <form method="POST" class="admin-form validate-form">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
      <input type="hidden" name="qs" value="<?= htmlspecialchars($qs) ?>">
      <input type="hidden" name="id" value="">

      <div class="form-group">
        <input type="text" name="name" placeholder="Client Name" data-validate="name">
        <small class="error"></small>
      </div>
      <div class="form-group">
        <input type="text" name="service" placeholder="Service" data-validate="name">
        <small class="error"></small>
      </div>
      <div class="form-group">
        <textarea name="review" data-validate="text10"></textarea>
        <small class="error"></small>
      </div>

      <label>Rating</label>
      <select name="rating">
        <?php for ($r = 5; $r >= 1; $r--): ?>
          <option value="<?= $r ?>"><?= $r ?> ★</option>
        <?php endfor; ?>
      </select>

      <label>Status</label>
      <select name="status">
        <option value="1">Active</option>
        <option value="0">Inactive</option>
      </select>

      <button type="submit" name="update">Update Testimonial</button>
    </form>
  </div>
</div>

<script src="assets/js/form-validator.js"></script>
<script src="assets/js/admin-alert.js"></script>
<script src="assets/js/admin-crud.js"></script>

<?php include 'includes/footer.php'; ?>
<script src="../assets/js/confirmation.js"></script>
