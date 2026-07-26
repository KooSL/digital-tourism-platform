<?php
include '../config/db.php';
include 'auth.php';

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

/* DELETE */
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if (mysqli_query($conn, "DELETE FROM buses WHERE id=$id")) {
        $_SESSION['success'] = "Bus deleted successfully.";
    } else {
        $_SESSION['error'] = "Failed to delete bus.";
    }
    header("Location: manage-buses" . (isset($_GET['qs']) ? '?' . $_GET['qs'] : ''));
    exit();
}

/* ADD */
if (isset($_POST['submit'])) {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("CSRF validation failed.");
    }

    $bus_name       = trim($_POST['bus_name']);
    $bus_number     = trim($_POST['bus_number']);
    $from           = trim($_POST['from_location']);
    $to             = trim($_POST['to_location']);
    $travel_date    = $_POST['travel_date'];
    $departure_time = $_POST['departure_time'];
    $arrival_time   = $_POST['arrival_time'];
    $price          = (float)$_POST['price'];
    $total_seats    = (int)$_POST['total_seats'];
    $desc           = trim($_POST['description']);
    $status         = (int)$_POST['status'];

    if ($bus_name === '' || $from === '' || $to === '' || empty($_FILES['image']['name'])) {
        $_SESSION['error'] = "Bus name, route and image are required.";
        $_SESSION['reopen_modal'] = 'addModal';
    } else {
        $imageName = time() . '_' . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], "../uploads/images/buses/" . $imageName);

        $stmt = $conn->prepare(
            "INSERT INTO buses
            (bus_name, bus_number, from_location, to_location, travel_date, departure_time, arrival_time, price, total_seats, description, banner_image, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param(
            "sssssssdissi",
            $bus_name, $bus_number, $from, $to, $travel_date, $departure_time, $arrival_time,
            $price, $total_seats, $desc, $imageName, $status
        );

        if ($stmt->execute()) {
            $_SESSION['success'] = "Bus added successfully.";
        } else {
            $_SESSION['error'] = "Failed to add bus.";
        }
        $stmt->close();
    }

    header("Location: manage-buses" . (!empty($_POST['qs']) ? '?' . $_POST['qs'] : ''));
    exit();
}

/* UPDATE */
if (isset($_POST['update'])) {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("CSRF validation failed.");
    }

    $id = (int)$_POST['id'];
    $existing = mysqli_fetch_assoc(mysqli_query($conn, "SELECT banner_image FROM buses WHERE id=$id"));

    if ($id <= 0 || !$existing) {
        $_SESSION['error'] = "Invalid bus.";
        $_SESSION['reopen_modal'] = 'editModal';
        header("Location: manage-buses" . (!empty($_POST['qs']) ? '?' . $_POST['qs'] : ''));
        exit();
    }

    $bus_name       = trim($_POST['bus_name']);
    $bus_number     = trim($_POST['bus_number']);
    $from           = trim($_POST['from_location']);
    $to             = trim($_POST['to_location']);
    $travel_date    = $_POST['travel_date'];
    $departure_time = $_POST['departure_time'];
    $arrival_time   = $_POST['arrival_time'];
    $price          = (float)$_POST['price'];
    $total_seats    = (int)$_POST['total_seats'];
    $desc           = trim($_POST['description']);
    $status         = (int)$_POST['status'];
    $image          = $existing['banner_image'];
    $upload_error   = '';

    if (!empty($_FILES['image']['name'])) {
        $max_size = 2 * 1024 * 1024;
        $allowed_types = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        $tmp = $_FILES['image']['tmp_name'];
        $size = $_FILES['image']['size'];
        $mime = mime_content_type($tmp);
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

        if ($size > $max_size) {
            $upload_error = "Image must be less than 2MB.";
        } elseif (!in_array($mime, $allowed_types)) {
            $upload_error = "Invalid image type.";
        } elseif (!getimagesize($tmp)) {
            $upload_error = "Invalid image file.";
        } else {
            $newImage = bin2hex(random_bytes(8)) . "_" . time() . "." . $ext;
            if (move_uploaded_file($tmp, "../uploads/images/buses/" . $newImage)) {
                $old = "../uploads/images/buses/" . $image;
                if (!empty($image) && file_exists($old)) unlink($old);
                $image = $newImage;
            } else {
                $upload_error = "Upload failed.";
            }
        }
    }

    if ($bus_name === '' || $from === '' || $to === '' || $upload_error !== '') {
        $_SESSION['error'] = $upload_error ?: "Bus name and route are required.";
        $_SESSION['reopen_modal'] = 'editModal';
    } else {
        $stmt = $conn->prepare("UPDATE buses SET
            bus_name=?, bus_number=?, from_location=?, to_location=?, travel_date=?,
            departure_time=?, arrival_time=?, price=?, total_seats=?, description=?, banner_image=?, status=?
            WHERE id=?");
        $stmt->bind_param(
            "sssssssdissii",
            $bus_name, $bus_number, $from, $to, $travel_date, $departure_time, $arrival_time,
            $price, $total_seats, $desc, $image, $status, $id
        );
        if ($stmt->execute()) {
            $_SESSION['success'] = "Bus updated successfully.";
        } else {
            $_SESSION['error'] = "Failed to update bus.";
        }
        $stmt->close();
    }

    header("Location: manage-buses" . (!empty($_POST['qs']) ? '?' . $_POST['qs'] : ''));
    exit();
}

/* SEARCH + FILTER + PAGINATION */
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$status = isset($_GET['status']) && $_GET['status'] !== '' ? (int)$_GET['status'] : null;
$from_f = isset($_GET['from']) ? trim($_GET['from']) : '';
$to_f   = isset($_GET['to']) ? trim($_GET['to']) : '';

$where = [];
$types = '';
$params = [];

if ($search !== '') {
    $where[] = "(bus_name LIKE ? OR bus_number LIKE ?)";
    $like = "%$search%";
    $types .= 'ss';
    $params[] = $like; $params[] = $like;
}
if ($status !== null) {
    $where[] = "status = ?";
    $types .= 'i';
    $params[] = $status;
}
if ($from_f !== '') {
    $where[] = "from_location LIKE ?";
    $types .= 's';
    $params[] = "%$from_f%";
}
if ($to_f !== '') {
    $where[] = "to_location LIKE ?";
    $types .= 's';
    $params[] = "%$to_f%";
}

$whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

$limit = 10;
$page = isset($_GET['page']) ? max((int)$_GET['page'], 1) : 1;
$offset = ($page - 1) * $limit;

$countStmt = $conn->prepare("SELECT COUNT(*) AS total FROM buses $whereSql");
if ($types) $countStmt->bind_param($types, ...$params);
$countStmt->execute();
$totalRows = $countStmt->get_result()->fetch_assoc()['total'];
$countStmt->close();
$totalPages = max((int)ceil($totalRows / $limit), 1);

$dataStmt = $conn->prepare("SELECT * FROM buses $whereSql ORDER BY id DESC LIMIT ? OFFSET ?");
$allTypes = $types . 'ii';
$allParams = array_merge($params, [$limit, $offset]);
$dataStmt->bind_param($allTypes, ...$allParams);
$dataStmt->execute();
$result = $dataStmt->get_result();

$qsArray = array_filter([
    'search' => $search,
    'status' => $status !== null ? $status : '',
    'from'   => $from_f,
    'to'     => $to_f,
], fn($v) => $v !== '');
$qs = http_build_query($qsArray);

include 'includes/header.php';
include 'includes/sidebar.php';

$reopenModal = $_SESSION['reopen_modal'] ?? '';
unset($_SESSION['reopen_modal']);
?>
<link rel="stylesheet" href="assets/css/admin-crud.css">

<div class="admin-content" data-reopen-modal="<?= htmlspecialchars($reopenModal) ?>">
  <h2>Buses</h2>

  <?php include 'includes/admin-alert.php'; ?>

  <div class="crud-toolbar">
    <form class="toolbar-filters" method="GET">
      <div class="search-input">
        <input type="text" name="search" placeholder="Search bus name / number..." value="<?= htmlspecialchars($search) ?>">
      </div>

      <input type="text" name="from" placeholder="From location" value="<?= htmlspecialchars($from_f) ?>" style="padding:10px 14px;border:1px solid #dde3ea;border-radius:8px;min-width:140px;">
      <input type="text" name="to" placeholder="To location" value="<?= htmlspecialchars($to_f) ?>" style="padding:10px 14px;border:1px solid #dde3ea;border-radius:8px;min-width:140px;">

      <select name="status" class="auto-submit">
        <option value="">All Status</option>
        <option value="1" <?= $status === 1 ? 'selected' : '' ?>>Active</option>
        <option value="0" <?= $status === 0 ? 'selected' : '' ?>>Inactive</option>
      </select>

      <button type="submit" class="btn-filter"><i class="fa-solid fa-filter"></i> Filter</button>
      <?php if ($search !== '' || $status !== null || $from_f !== '' || $to_f !== ''): ?>
        <a href="manage-buses" class="btn-reset">Reset</a>
      <?php endif; ?>
    </form>

    <button type="button" class="btn-add" onclick="openAddModal()"><i class="fa-solid fa-plus"></i> Add Bus</button>
  </div>

  <p class="result-count"><?= $totalRows ?> bus<?= $totalRows == 1 ? '' : 'es' ?> found</p>

  <div class="table-scroll"><table class="admin-table">
    <thead>
      <tr>
        <th>S.N.</th>
        <th>Created Date</th>
        <th>Bus Name</th>
        <th>Bus Number</th>
        <th>From</th>
        <th>To</th>
        <th>Travel Date</th>
        <th>Departure</th>
        <th>Arrival</th>
        <th>Price</th>
        <th>Seats</th>
        <th>Description</th>
        <th>Image</th>
        <th>Status</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php $i = $offset + 1; ?>
      <?php if ($totalRows == 0): ?>
        <tr><td colspan="15" class="no-results">No buses match your search/filter.</td></tr>
      <?php endif; ?>
      <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
          <td><?= $i++ ?></td>
          <td><?= htmlspecialchars($row['created_at']) ?></td>
          <td><?= htmlspecialchars($row['bus_name']) ?></td>
          <td><?= htmlspecialchars($row['bus_number']) ?></td>
          <td><?= htmlspecialchars($row['from_location']) ?></td>
          <td><?= htmlspecialchars($row['to_location']) ?></td>
          <td><?= htmlspecialchars($row['travel_date']) ?></td>
          <td><?= htmlspecialchars($row['departure_time']) ?></td>
          <td><?= htmlspecialchars($row['arrival_time']) ?></td>
          <td><?= htmlspecialchars($row['price']) ?></td>
          <td><?= htmlspecialchars($row['total_seats']) ?></td>
          <td><?= implode(' ', array_slice(explode(' ', $row['description']), 0, 5)) ?>...</td>
          <td><img src="../uploads/images/buses/<?= htmlspecialchars($row['banner_image']) ?>" height="50"></td>
          <td class="status-col"><span class="pill <?= $row['status'] ? 'published' : 'draft' ?>"><?= $row['status'] ? 'Active' : 'Inactive' ?></span></td>
          <td class="action-col">
            <button type="button" class="btn-edit"
              onclick="openEditModal(this)"
              data-id="<?= $row['id'] ?>"
              data-bus_name="<?= htmlspecialchars($row['bus_name']) ?>"
              data-bus_number="<?= htmlspecialchars($row['bus_number']) ?>"
              data-from_location="<?= htmlspecialchars($row['from_location']) ?>"
              data-to_location="<?= htmlspecialchars($row['to_location']) ?>"
              data-travel_date="<?= htmlspecialchars($row['travel_date']) ?>"
              data-departure_time="<?= htmlspecialchars($row['departure_time']) ?>"
              data-arrival_time="<?= htmlspecialchars($row['arrival_time']) ?>"
              data-price="<?= htmlspecialchars($row['price']) ?>"
              data-total_seats="<?= htmlspecialchars($row['total_seats']) ?>"
              data-description="<?= htmlspecialchars($row['description']) ?>"
              data-status="<?= $row['status'] ?>"
              data-image="<?= htmlspecialchars($row['banner_image']) ?>"
              data-image-path="../uploads/images/buses/<?= htmlspecialchars($row['banner_image']) ?>">
              Edit
            </button>
            <a href="javascript:void(0)"
              onclick="showConfirm('?delete=<?= $row['id'] ?>&qs=<?= urlencode($qs) ?>','Delete this bus?')"
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
    <h2>Add New Bus</h2>
    <form method="POST" enctype="multipart/form-data" class="admin-form validate-form">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
      <input type="hidden" name="qs" value="<?= htmlspecialchars($qs) ?>">

      <div class="form-group"><input type="text" name="bus_name" placeholder="Bus Name" data-validate="name"><small class="error"></small></div>
      <div class="form-group"><input type="text" name="bus_number" placeholder="Bus Number"><small class="error"></small></div>
      <div class="form-group"><input type="text" name="from_location" placeholder="From Location" data-validate="city"><small class="error"></small></div>
      <div class="form-group"><input type="text" name="to_location" placeholder="To Location" data-validate="city"><small class="error"></small></div>

      <label>Travel Date</label>
      <div class="form-group"><input type="date" name="travel_date" data-validate="date" min="<?= date('Y-m-d') ?>"><small class="error"></small></div>

      <label>Departure Time</label>
      <div class="form-group"><input type="time" name="departure_time" data-validate="time"><small class="error"></small></div>

      <label>Arrival Time</label>
      <div class="form-group"><input type="time" name="arrival_time" data-validate="time"><small class="error"></small></div>

      <div class="form-group"><input type="text" name="price" placeholder="Price" data-validate="number"><small class="error"></small></div>
      <div class="form-group"><input type="number" name="total_seats" placeholder="Total Seats" data-validate="number"><small class="error"></small></div>
      <div class="form-group"><textarea name="description" placeholder="Description" data-validate="text20"></textarea><small class="error"></small></div>

      <div class="file_input">
        <label>Bus Image</label>
        <input type="file" name="image" accept="image/*" required>
      </div>

      <label>Status</label>
      <select name="status" required>
        <option value="1">Active</option>
        <option value="0">Inactive</option>
      </select>

      <button type="submit" name="submit">Add Bus</button>
    </form>
  </div>
</div>

<!-- EDIT MODAL -->
<div class="crud-modal-overlay" id="editModal">
  <div class="crud-modal">
    <button type="button" class="modal-close" onclick="closeModal('editModal')">&times;</button>
    <h2>Edit Bus</h2>
    <form method="POST" enctype="multipart/form-data" class="admin-form validate-form">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
      <input type="hidden" name="qs" value="<?= htmlspecialchars($qs) ?>">
      <input type="hidden" name="id" value="">

      <div class="form-group"><input type="text" name="bus_name" placeholder="Bus Name" data-validate="name"><small class="error"></small></div>
      <div class="form-group"><input type="text" name="bus_number" placeholder="Bus Number"><small class="error"></small></div>
      <div class="form-group"><input type="text" name="from_location" placeholder="From" data-validate="city"><small class="error"></small></div>
      <div class="form-group"><input type="text" name="to_location" placeholder="To" data-validate="city"><small class="error"></small></div>

      <label>Travel Date</label>
      <div class="form-group"><input type="date" name="travel_date" data-validate="date" min="<?= date('Y-m-d') ?>"><small class="error"></small></div>

      <label>Departure Time</label>
      <div class="form-group"><input type="time" name="departure_time" data-validate="time"><small class="error"></small></div>

      <label>Arrival Time</label>
      <div class="form-group"><input type="time" name="arrival_time" data-validate="time"><small class="error"></small></div>

      <div class="form-group"><input type="number" name="price" data-validate="number"><small class="error"></small></div>
      <div class="form-group"><input type="number" name="total_seats" data-validate="number"><small class="error"></small></div>
      <div class="form-group"><textarea name="description" data-validate="text20"></textarea><small class="error"></small></div>

      <label>Current Image</label>
      <div class="current-image">
        <img src="" width="120" alt="">
      </div>

      <div class="file_input">
        <label>Change Image</label>
        <input type="file" name="image">
      </div>

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
