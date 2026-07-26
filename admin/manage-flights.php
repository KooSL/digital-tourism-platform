<?php
include '../config/db.php';
include 'auth.php';

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

/* DELETE */
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if (mysqli_query($conn, "DELETE FROM flights WHERE id=$id")) {
        $_SESSION['success'] = "Flight deleted successfully.";
    } else {
        $_SESSION['error'] = "Failed to delete flight.";
    }
    header("Location: manage-flights" . (isset($_GET['qs']) ? '?' . $_GET['qs'] : ''));
    exit();
}

/* ADD */
if (isset($_POST['submit'])) {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("CSRF validation failed.");
    }

    $from = trim($_POST['from_city']);
    $to = trim($_POST['to_city']);
    $desc = trim($_POST['description']);
    $group_fare = (int)$_POST['group_fare'];
    $status = (int)$_POST['status'];

    if ($from === '' || $to === '' || empty($_FILES['image']['name'])) {
        $_SESSION['error'] = "From city, to city and image are required.";
        $_SESSION['reopen_modal'] = 'addModal';
    } else {
        $imageName = time() . '_' . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], "../uploads/images/flights/" . $imageName);

        $stmt = $conn->prepare("INSERT INTO flights (from_city, to_city, description, image, is_group_fare, status) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssii", $from, $to, $desc, $imageName, $group_fare, $status);
        if ($stmt->execute()) {
            $_SESSION['success'] = "Flight added successfully.";
        } else {
            $_SESSION['error'] = "Failed to add flight.";
        }
        $stmt->close();
    }

    header("Location: manage-flights" . (!empty($_POST['qs']) ? '?' . $_POST['qs'] : ''));
    exit();
}

/* UPDATE */
if (isset($_POST['update'])) {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("CSRF validation failed.");
    }

    $id = (int)$_POST['id'];
    $existing = mysqli_fetch_assoc(mysqli_query($conn, "SELECT image FROM flights WHERE id=$id"));

    if ($id <= 0 || !$existing) {
        $_SESSION['error'] = "Invalid flight.";
        $_SESSION['reopen_modal'] = 'editModal';
        header("Location: manage-flights" . (!empty($_POST['qs']) ? '?' . $_POST['qs'] : ''));
        exit();
    }

    $from = trim($_POST['from_city']);
    $to = trim($_POST['to_city']);
    $desc = trim($_POST['description']);
    $group_fare = (int)$_POST['group_fare'];
    $status = (int)$_POST['status'];
    $image = $existing['image'];

    if (!empty($_FILES['image']['name'])) {
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $newImage = time() . '_' . basename($_FILES['image']['name']);
        if (move_uploaded_file($_FILES['image']['tmp_name'], "../uploads/images/flights/" . $newImage)) {
            $old = "../uploads/images/flights/" . $image;
            if (!empty($image) && file_exists($old)) unlink($old);
            $image = $newImage;
        }
    }

    if ($from === '' || $to === '') {
        $_SESSION['error'] = "From city and to city are required.";
        $_SESSION['reopen_modal'] = 'editModal';
    } else {
        $stmt = $conn->prepare("UPDATE flights SET from_city=?, to_city=?, description=?, image=?, is_group_fare=?, status=? WHERE id=?");
        $stmt->bind_param("ssssiii", $from, $to, $desc, $image, $group_fare, $status, $id);
        if ($stmt->execute()) {
            $_SESSION['success'] = "Flight updated successfully.";
        } else {
            $_SESSION['error'] = "Failed to update flight.";
        }
        $stmt->close();
    }

    header("Location: manage-flights" . (!empty($_POST['qs']) ? '?' . $_POST['qs'] : ''));
    exit();
}

/* SEARCH + FILTER + PAGINATION */
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$status = isset($_GET['status']) && $_GET['status'] !== '' ? (int)$_GET['status'] : null;
$group_fare = isset($_GET['group_fare']) && $_GET['group_fare'] !== '' ? (int)$_GET['group_fare'] : null;

$where = [];
$types = '';
$params = [];

if ($search !== '') {
    $where[] = "(from_city LIKE ? OR to_city LIKE ?)";
    $like = "%$search%";
    $types .= 'ss';
    $params[] = $like; $params[] = $like;
}
if ($status !== null) {
    $where[] = "status = ?";
    $types .= 'i';
    $params[] = $status;
}
if ($group_fare !== null) {
    $where[] = "is_group_fare = ?";
    $types .= 'i';
    $params[] = $group_fare;
}

$whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

$limit = 10;
$page = isset($_GET['page']) ? max((int)$_GET['page'], 1) : 1;
$offset = ($page - 1) * $limit;

$countStmt = $conn->prepare("SELECT COUNT(*) AS total FROM flights $whereSql");
if ($types) $countStmt->bind_param($types, ...$params);
$countStmt->execute();
$totalRows = $countStmt->get_result()->fetch_assoc()['total'];
$countStmt->close();
$totalPages = max((int)ceil($totalRows / $limit), 1);

$dataStmt = $conn->prepare("SELECT * FROM flights $whereSql ORDER BY id DESC LIMIT ? OFFSET ?");
$allTypes = $types . 'ii';
$allParams = array_merge($params, [$limit, $offset]);
$dataStmt->bind_param($allTypes, ...$allParams);
$dataStmt->execute();
$result = $dataStmt->get_result();

$qsArray = array_filter([
    'search' => $search,
    'status' => $status !== null ? $status : '',
    'group_fare' => $group_fare !== null ? $group_fare : '',
], fn($v) => $v !== '');
$qs = http_build_query($qsArray);

include 'includes/header.php';
include 'includes/sidebar.php';

$reopenModal = $_SESSION['reopen_modal'] ?? '';
unset($_SESSION['reopen_modal']);
?>
<link rel="stylesheet" href="assets/css/admin-crud.css">

<div class="admin-content" data-reopen-modal="<?= htmlspecialchars($reopenModal) ?>">
  <h2>Flights</h2>

  <?php include 'includes/admin-alert.php'; ?>

  <div class="crud-toolbar">
    <form class="toolbar-filters" method="GET">
      <div class="search-input">
        <input type="text" name="search" placeholder="Search from/to city..." value="<?= htmlspecialchars($search) ?>">
      </div>

      <select name="group_fare" class="auto-submit">
        <option value="">Group Fare?</option>
        <option value="1" <?= $group_fare === 1 ? 'selected' : '' ?>>Yes</option>
        <option value="0" <?= $group_fare === 0 ? 'selected' : '' ?>>No</option>
      </select>

      <select name="status" class="auto-submit">
        <option value="">All Status</option>
        <option value="1" <?= $status === 1 ? 'selected' : '' ?>>Active</option>
        <option value="0" <?= $status === 0 ? 'selected' : '' ?>>Inactive</option>
      </select>

      <button type="submit" class="btn-filter"><i class="fa-solid fa-filter"></i> Filter</button>
      <?php if ($search !== '' || $status !== null || $group_fare !== null): ?>
        <a href="manage-flights" class="btn-reset">Reset</a>
      <?php endif; ?>
    </form>

    <button type="button" class="btn-add" onclick="openAddModal()"><i class="fa-solid fa-plus"></i> Add Flight</button>
  </div>

  <p class="result-count"><?= $totalRows ?> flight<?= $totalRows == 1 ? '' : 's' ?> found</p>

  <div class="table-scroll"><table class="admin-table">
    <thead>
      <tr>
        <th>S.N.</th>
        <th>Created Date</th>
        <th>From City</th>
        <th>To City</th>
        <th>Description</th>
        <th>Image</th>
        <th>Group Fare</th>
        <th>Status</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php $i = $offset + 1; ?>
      <?php if ($totalRows == 0): ?>
        <tr><td colspan="9" class="no-results">No flights match your search/filter.</td></tr>
      <?php endif; ?>
      <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
          <td><?= $i++ ?></td>
          <td><?= htmlspecialchars($row['created_at']) ?></td>
          <td><?= htmlspecialchars($row['from_city']) ?></td>
          <td><?= htmlspecialchars($row['to_city']) ?></td>
          <td><?= implode(' ', array_slice(explode(' ', $row['description']), 0, 5)) ?>...</td>
          <td><img src="../uploads/images/flights/<?= htmlspecialchars($row['image']) ?>" height="50"></td>
          <td><?= $row['is_group_fare'] ? 'Yes' : 'No' ?></td>
          <td class="status-col"><span class="pill <?= $row['status'] ? 'published' : 'draft' ?>"><?= $row['status'] ? 'Active' : 'Inactive' ?></span></td>
          <td class="action-col-flight">
            <button type="button" class="btn-edit"
              onclick="openEditModal(this)"
              data-id="<?= $row['id'] ?>"
              data-from_city="<?= htmlspecialchars($row['from_city']) ?>"
              data-to_city="<?= htmlspecialchars($row['to_city']) ?>"
              data-description="<?= htmlspecialchars($row['description']) ?>"
              data-group_fare="<?= $row['is_group_fare'] ?>"
              data-status="<?= $row['status'] ?>"
              data-image="<?= htmlspecialchars($row['image']) ?>"
              data-image-path="../uploads/images/flights/<?= htmlspecialchars($row['image']) ?>">
              Edit
            </button>
            <a href="javascript:void(0)"
              onclick="showConfirm('?delete=<?= $row['id'] ?>&qs=<?= urlencode($qs) ?>','Delete this flight?')"
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
    <h2>Add Flight</h2>
    <form method="POST" enctype="multipart/form-data" class="admin-form validate-form">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
      <input type="hidden" name="qs" value="<?= htmlspecialchars($qs) ?>">

      <div class="form-group"><input type="text" name="from_city" placeholder="From City" data-validate="city"><small class="error"></small></div>
      <div class="form-group"><input type="text" name="to_city" placeholder="To City" data-validate="city"><small class="error"></small></div>
      <div class="form-group"><textarea name="description" placeholder="Description" data-validate="text20"></textarea><small class="error"></small></div>

      <div class="file_input">
        <label>Flight Image</label>
        <input type="file" name="image" accept="image/*" required>
      </div>

      <label>Group Fare</label>
      <select name="group_fare" required>
        <option value="1">Yes</option>
        <option value="0">No</option>
      </select>

      <label>Status</label>
      <select name="status" required>
        <option value="1">Active</option>
        <option value="0">Inactive</option>
      </select>

      <button type="submit" name="submit">Add Flight</button>
    </form>
  </div>
</div>

<!-- EDIT MODAL -->
<div class="crud-modal-overlay" id="editModal">
  <div class="crud-modal">
    <button type="button" class="modal-close" onclick="closeModal('editModal')">&times;</button>
    <h2>Edit Flight</h2>
    <form method="POST" enctype="multipart/form-data" class="admin-form validate-form">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
      <input type="hidden" name="qs" value="<?= htmlspecialchars($qs) ?>">
      <input type="hidden" name="id" value="">

      <div class="form-group"><input type="text" name="from_city" placeholder="From City" data-validate="city"><small class="error"></small></div>
      <div class="form-group"><input type="text" name="to_city" placeholder="To City" data-validate="city"><small class="error"></small></div>
      <div class="form-group"><textarea name="description" data-validate="text20"></textarea><small class="error"></small></div>

      <label>Current Image</label>
      <div class="current-image">
        <img src="" width="120" alt="">
      </div>

      <div class="file_input">
        <label>Change Image</label>
        <input type="file" name="image">
      </div>

      <label>Group Fare</label>
      <select name="group_fare">
        <option value="1">Yes</option>
        <option value="0">No</option>
      </select>

      <label>Status</label>
      <select name="status">
        <option value="1">Active</option>
        <option value="0">Inactive</option>
      </select>

      <button type="submit" name="update">Update</button>
    </form>
  </div>
</div>

<script src="assets/js/form-validator.js"></script>
<script src="assets/js/admin-alert.js"></script>
<script src="assets/js/admin-crud.js"></script>

<?php include 'includes/footer.php'; ?>
<script src="../assets/js/confirmation.js"></script>
