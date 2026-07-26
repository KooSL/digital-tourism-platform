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
    if (mysqli_query($conn, "DELETE FROM clients WHERE id=$id")) {
        $_SESSION['success'] = "Client deleted successfully.";
    } else {
        $_SESSION['error'] = "Failed to delete client.";
    }
    header("Location: manage-clients" . (isset($_GET['qs']) ? '?' . $_GET['qs'] : ''));
    exit();
}

/* ---------------------------------------------------------
   ADD
--------------------------------------------------------- */
if (isset($_POST['submit'])) {

    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("CSRF validation failed.");
    }

    $name   = trim($_POST['name']);
    $status = (int)$_POST['status'];

    if ($name === '' || empty($_FILES['logo']['name'])) {
        $_SESSION['error'] = "Client name and logo are required.";
        $_SESSION['reopen_modal'] = 'addModal';
    } else {
        $logo = time() . '_' . basename($_FILES['logo']['name']);
        move_uploaded_file($_FILES['logo']['tmp_name'], "uploads/images/clients/" . $logo);

        $stmt = $conn->prepare("INSERT INTO clients (name, logo, status) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $name, $logo, $status);
        if ($stmt->execute()) {
            $_SESSION['success'] = "Client added successfully.";
        } else {
            $_SESSION['error'] = "Failed to add client.";
        }
        $stmt->close();
    }

    header("Location: manage-clients" . (!empty($_POST['qs']) ? '?' . $_POST['qs'] : ''));
    exit();
}

/* ---------------------------------------------------------
   UPDATE
--------------------------------------------------------- */
if (isset($_POST['update'])) {

    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("CSRF validation failed.");
    }

    $id     = (int)$_POST['id'];
    $name   = trim($_POST['name']);
    $status = (int)$_POST['status'];

    $existing = mysqli_fetch_assoc(mysqli_query($conn, "SELECT logo FROM clients WHERE id=$id"));

    if ($id <= 0 || $name === '' || !$existing) {
        $_SESSION['error'] = "Invalid client.";
        $_SESSION['reopen_modal'] = 'editModal';
    } else {
        $logo = $existing['logo'];

        if (!empty($_FILES['logo']['name'])) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
            if (in_array($_FILES['logo']['type'], $allowedTypes)) {
                $ext = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
                $newLogo = time() . '_client.' . $ext;
                if (move_uploaded_file($_FILES['logo']['tmp_name'], "uploads/images/clients/" . $newLogo)) {
                    @unlink("uploads/images/clients/" . $logo);
                    $logo = $newLogo;
                }
            }
        }

        $stmt = $conn->prepare("UPDATE clients SET name=?, logo=?, status=? WHERE id=?");
        $stmt->bind_param("ssii", $name, $logo, $status, $id);
        if ($stmt->execute()) {
            $_SESSION['success'] = "Client updated successfully.";
        } else {
            $_SESSION['error'] = "Failed to update client.";
        }
        $stmt->close();
    }

    header("Location: manage-clients" . (!empty($_POST['qs']) ? '?' . $_POST['qs'] : ''));
    exit();
}

/* ---------------------------------------------------------
   SEARCH + FILTER + PAGINATION
--------------------------------------------------------- */
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$status = isset($_GET['status']) && $_GET['status'] !== '' ? (int)$_GET['status'] : null;

$where  = [];
$types  = '';
$params = [];

if ($search !== '') {
    $where[] = "name LIKE ?";
    $types .= 's';
    $params[] = "%$search%";
}
if ($status !== null) {
    $where[] = "status = ?";
    $types .= 'i';
    $params[] = $status;
}

$whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

$limit = 10;
$page  = isset($_GET['page']) ? max((int)$_GET['page'], 1) : 1;
$offset = ($page - 1) * $limit;

$countStmt = $conn->prepare("SELECT COUNT(*) AS total FROM clients $whereSql");
if ($types) $countStmt->bind_param($types, ...$params);
$countStmt->execute();
$totalRows = $countStmt->get_result()->fetch_assoc()['total'];
$countStmt->close();
$totalPages = max((int)ceil($totalRows / $limit), 1);

$dataStmt = $conn->prepare("SELECT * FROM clients $whereSql ORDER BY id DESC LIMIT ? OFFSET ?");
$allTypes = $types . 'ii';
$allParams = array_merge($params, [$limit, $offset]);
$dataStmt->bind_param($allTypes, ...$allParams);
$dataStmt->execute();
$result = $dataStmt->get_result();

$qsArray = array_filter([
    'search' => $search,
    'status' => $status !== null ? $status : '',
], fn($v) => $v !== '');
$qs = http_build_query($qsArray);

include 'includes/header.php';
include 'includes/sidebar.php';

$reopenModal = $_SESSION['reopen_modal'] ?? '';
unset($_SESSION['reopen_modal']);
?>
<link rel="stylesheet" href="assets/css/admin-crud.css">

<div class="admin-content" data-reopen-modal="<?= htmlspecialchars($reopenModal) ?>">
  <h2>Clients</h2>

  <?php include 'includes/admin-alert.php'; ?>

  <div class="crud-toolbar">
    <form class="toolbar-filters" method="GET">
      <div class="search-input">
        <input type="text" name="search" placeholder="Search by name..." value="<?= htmlspecialchars($search) ?>">
      </div>

      <select name="status" class="auto-submit">
        <option value="">All Status</option>
        <option value="1" <?= $status === 1 ? 'selected' : '' ?>>Active</option>
        <option value="0" <?= $status === 0 ? 'selected' : '' ?>>Inactive</option>
      </select>

      <button type="submit" class="btn-filter"><i class="fa-solid fa-filter"></i> Filter</button>
      <?php if ($search !== '' || $status !== null): ?>
        <a href="manage-clients" class="btn-reset">Reset</a>
      <?php endif; ?>
    </form>

    <button type="button" class="btn-add" onclick="openAddModal()"><i class="fa-solid fa-plus"></i> Add Client</button>
  </div>

  <p class="result-count"><?= $totalRows ?> client<?= $totalRows == 1 ? '' : 's' ?> found</p>

  <div class="table-scroll"><table class="admin-table">
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
      <?php $i = $offset + 1; ?>
      <?php if ($totalRows == 0): ?>
        <tr><td colspan="5" class="no-results">No clients match your search/filter.</td></tr>
      <?php endif; ?>
      <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
          <td><?= $i++ ?></td>
          <td><?= htmlspecialchars($row['name']) ?></td>
          <td><img src="uploads/images/clients/<?= htmlspecialchars($row['logo']) ?>" height="50"></td>
          <td class="status-col"><span class="pill <?= $row['status'] ? 'published' : 'draft' ?>"><?= $row['status'] ? 'Active' : 'Inactive' ?></span></td>
          <td class="action-col">
            <button type="button" class="btn-edit"
              onclick="openEditModal(this)"
              data-id="<?= $row['id'] ?>"
              data-name="<?= htmlspecialchars($row['name']) ?>"
              data-status="<?= $row['status'] ?>"
              data-image="<?= htmlspecialchars($row['logo']) ?>"
              data-image-path="uploads/images/clients/<?= htmlspecialchars($row['logo']) ?>">
              Edit
            </button>
            <a href="javascript:void(0)"
              onclick="showConfirm('?delete=<?= $row['id'] ?>&qs=<?= urlencode($qs) ?>','Delete this client?')"
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
    <h2>Add Client</h2>
    <form method="POST" enctype="multipart/form-data" class="admin-form validate-form">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
      <input type="hidden" name="qs" value="<?= htmlspecialchars($qs) ?>">

      <div class="form-group">
        <input type="text" name="name" placeholder="Client Name" data-validate="name">
        <small class="error"></small>
      </div>

      <div class="file_input">
        <label>Client Logo</label>
        <input type="file" name="logo" accept="image/*" required>
      </div>

      <label>Status</label>
      <select name="status">
        <option value="1">Active</option>
        <option value="0">Inactive</option>
      </select>

      <button type="submit" name="submit">Add Client</button>
    </form>
  </div>
</div>

<!-- EDIT MODAL -->
<div class="crud-modal-overlay" id="editModal">
  <div class="crud-modal">
    <button type="button" class="modal-close" onclick="closeModal('editModal')">&times;</button>
    <h2>Edit Client</h2>
    <form method="POST" enctype="multipart/form-data" class="admin-form validate-form">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
      <input type="hidden" name="qs" value="<?= htmlspecialchars($qs) ?>">
      <input type="hidden" name="id" value="">

      <div class="form-group">
        <input type="text" name="name" placeholder="Client Name" data-validate="name">
        <small class="error"></small>
      </div>

      <label>Current Logo</label>
      <div class="current-image">
        <img src="" width="120" alt="">
      </div>

      <div class="file_input">
        <label>Change Logo</label>
        <input type="file" name="logo" accept="image/*">
      </div>

      <label>Status</label>
      <select name="status">
        <option value="1">Active</option>
        <option value="0">Inactive</option>
      </select>

      <button type="submit" name="update">Update Client</button>
    </form>
  </div>
</div>

<script src="assets/js/form-validator.js"></script>
<script src="assets/js/admin-alert.js"></script>
<script src="assets/js/admin-crud.js"></script>

<?php include 'includes/footer.php'; ?>
<script src="../assets/js/confirmation.js"></script>
