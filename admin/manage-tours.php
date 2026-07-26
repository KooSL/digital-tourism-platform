<?php
include '../config/db.php';
include 'auth.php';

if (empty($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

/* DELETE */
if (isset($_GET['delete'])) {
  $id = (int)$_GET['delete'];
  if (mysqli_query($conn, "DELETE FROM tours WHERE id=$id")) {
    $_SESSION['success'] = "Tour deleted successfully.";
  } else {
    $_SESSION['error'] = "Failed to delete tour.";
  }
  header("Location: manage-tours" . (isset($_GET['qs']) ? '?' . $_GET['qs'] : ''));
  exit;
}

function slugifyTour($text)
{
  $text = strtolower(trim($text));
  $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
  $text = preg_replace('/[\s-]+/', '-', $text);
  return trim($text, '-');
}

function saveItineraries($conn, $tourId)
{
  $conn->query("DELETE FROM tour_itineraries WHERE tour_id = $tourId");

  if (empty($_POST['day_no'])) return;

  $days = $_POST['day_no'];
  $titles = $_POST['itinerary_title'];
  $descs = $_POST['itinerary_desc'];

  $itStmt = $conn->prepare("INSERT INTO tour_itineraries (tour_id, day_number, title, description) VALUES (?, ?, ?, ?)");
  for ($i = 0; $i < count($days); $i++) {
    $day = (int)$days[$i];
    $itTitle = trim($titles[$i] ?? '');
    $itDesc = trim($descs[$i] ?? '');
    if ($day && $itTitle && $itDesc) {
      $itStmt->bind_param("iiss", $tourId, $day, $itTitle, $itDesc);
      $itStmt->execute();
    }
  }
  $itStmt->close();
}

/* ADD */
if (isset($_POST['submit'])) {
  if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    die("CSRF validation failed.");
  }

  $title         = trim($_POST['title']);
  $slug          = trim($_POST['slug']) ?: slugifyTour($title);
  $type          = $_POST['type'];
  $duration      = trim($_POST['duration']);
  $price         = (float)$_POST['price'];
  $price_usd     = (float)$_POST['price_usd'];
  $old_price     = (float)($_POST['old_price'] ?? 0);
  $overview      = trim($_POST['overview']);
  $highlights    = trim($_POST['highlights']);
  $includes      = trim($_POST['includes']);
  $excludes      = trim($_POST['excludes']);
  $status        = (int)$_POST['status'];
  $is_popular    = (int)$_POST['is_popular'];
  $latitude      = (float)($_POST['latitude'] ?? 0);
  $longitude     = (float)($_POST['longitude'] ?? 0);
  $location_name = trim($_POST['location_name']);

  if ($title === '' || empty($_FILES['banner']['name'])) {
    $_SESSION['error'] = "Title and banner image are required.";
    $_SESSION['reopen_modal'] = 'addModal';
  } else {
    $banner = time() . '_' . preg_replace('/[^A-Za-z0-9._-]/', '', $_FILES['banner']['name']);
    move_uploaded_file($_FILES['banner']['tmp_name'], "../uploads/images/tours/" . $banner);

    $pdf = '';
    if (!empty($_FILES['pdf']['name'])) {
      $pdf = time() . '_' . preg_replace('/[^A-Za-z0-9._-]/', '', $_FILES['pdf']['name']);
      move_uploaded_file($_FILES['pdf']['tmp_name'], "../uploads/pdf/" . $pdf);
    }

    $stmt = $conn->prepare("
            INSERT INTO tours
            (title, slug, type, duration, price, price_usd, old_price, overview, highlights, includes, excludes, banner_image, pdf_file, is_popular, status, latitude, longitude, location_name)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
    $stmt->bind_param(
      "ssssdddssssssiidds",
      $title,
      $slug,
      $type,
      $duration,
      $price,
      $price_usd,
      $old_price,
      $overview,
      $highlights,
      $includes,
      $excludes,
      $banner,
      $pdf,
      $is_popular,
      $status,
      $latitude,
      $longitude,
      $location_name
    );

    if ($stmt->execute()) {
      saveItineraries($conn, $stmt->insert_id);
      $_SESSION['success'] = "Tour added successfully.";
    } else {
      $_SESSION['error'] = "Error adding tour.";
    }
    $stmt->close();
  }

  header("Location: manage-tours" . (!empty($_POST['qs']) ? '?' . $_POST['qs'] : ''));
  exit();
}

/* UPDATE */
if (isset($_POST['update'])) {
  if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    die("CSRF validation failed.");
  }

  $id = (int)$_POST['id'];
  $data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tours WHERE id=$id"));

  if ($id <= 0 || !$data) {
    $_SESSION['error'] = "Invalid tour.";
    $_SESSION['reopen_modal'] = 'editModal';
    header("Location: manage-tours" . (!empty($_POST['qs']) ? '?' . $_POST['qs'] : ''));
    exit();
  }

  $title         = trim($_POST['title']);
  $type          = $_POST['type'];
  $duration      = trim($_POST['duration']);
  $price         = (float)$_POST['price'];
  $price_usd     = (float)$_POST['price_usd'];
  $overview      = trim($_POST['overview']);
  $highlights    = trim($_POST['highlights']);
  $includes      = trim($_POST['includes']);
  $excludes      = trim($_POST['excludes']);
  $status        = (int)$_POST['status'];
  $is_popular    = (int)$_POST['is_popular'];
  $latitude      = (float)($_POST['latitude'] ?? 0);
  $longitude     = (float)($_POST['longitude'] ?? 0);
  $location_name = trim($_POST['location_name']);
  $image         = $data['banner_image'];
  $pdfName       = $data['pdf_file'];

  if (!empty($_FILES['banner']['name'])) {
    $image = time() . '_' . preg_replace('/[^A-Za-z0-9._-]/', '', $_FILES['banner']['name']);
    move_uploaded_file($_FILES['banner']['tmp_name'], "../uploads/images/tours/" . $image);
    @unlink("../uploads/images/tours/" . $data['banner_image']);
  }

  if (!empty($_FILES['pdf']['name'])) {
    $pdfName = time() . '_' . preg_replace('/[^A-Za-z0-9._-]/', '', $_FILES['pdf']['name']);
    move_uploaded_file($_FILES['pdf']['tmp_name'], "../uploads/pdf/" . $pdfName);
  }

  if ($title === '') {
    $_SESSION['error'] = "Title is required.";
    $_SESSION['reopen_modal'] = 'editModal';
  } else {
    $stmt = $conn->prepare("
            UPDATE tours SET
              title = ?, type = ?, duration = ?, price = ?, price_usd = ?,
              overview = ?, highlights = ?, includes = ?, excludes = ?,
              banner_image = ?, pdf_file = ?, is_popular = ?, status = ?,
              latitude = ?, longitude = ?, location_name = ?
            WHERE id = ?
        ");
    $stmt->bind_param(
      "sssddsssssssiddsi",
      $title,
      $type,
      $duration,
      $price,
      $price_usd,
      $overview,
      $highlights,
      $includes,
      $excludes,
      $image,
      $pdfName,
      $is_popular,
      $status,
      $latitude,
      $longitude,
      $location_name,
      $id
    );

    if ($stmt->execute()) {
      saveItineraries($conn, $id);
      $_SESSION['success'] = "Tour updated successfully.";
    } else {
      $_SESSION['error'] = "Failed to update tour.";
    }
    $stmt->close();
  }

  header("Location: manage-tours" . (!empty($_POST['qs']) ? '?' . $_POST['qs'] : ''));
  exit();
}

/* SEARCH + FILTER + PAGINATION */
$search   = isset($_GET['search']) ? trim($_GET['search']) : '';
$status   = isset($_GET['status']) && $_GET['status'] !== '' ? (int)$_GET['status'] : null;
$type     = isset($_GET['type']) && $_GET['type'] !== '' ? $_GET['type'] : '';
$popular  = isset($_GET['popular']) && $_GET['popular'] !== '' ? (int)$_GET['popular'] : null;

$where = [];
$types = '';
$params = [];

if ($search !== '') {
  $where[] = "(title LIKE ? OR location_name LIKE ?)";
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
if ($type !== '') {
  $where[] = "type = ?";
  $types .= 's';
  $params[] = $type;
}
if ($popular !== null) {
  $where[] = "is_popular = ?";
  $types .= 'i';
  $params[] = $popular;
}
$whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

$limit = 5;
$page = isset($_GET['page']) ? max((int)$_GET['page'], 1) : 1;
$offset = ($page - 1) * $limit;

$countStmt = $conn->prepare("SELECT COUNT(*) AS total FROM tours $whereSql");
if ($types) $countStmt->bind_param($types, ...$params);
$countStmt->execute();
$totalRows = $countStmt->get_result()->fetch_assoc()['total'];
$countStmt->close();
$totalPages = max(1, (int)ceil($totalRows / $limit));

$dataStmt = $conn->prepare("SELECT * FROM tours $whereSql ORDER BY id DESC LIMIT ? OFFSET ?");
$allTypes = $types . 'ii';
$allParams = array_merge($params, [$limit, $offset]);
$dataStmt->bind_param($allTypes, ...$allParams);
$dataStmt->execute();
$result = $dataStmt->get_result();

$qsArray = array_filter([
  'search' => $search,
  'status' => $status !== null ? $status : '',
  'type' => $type,
  'popular' => $popular !== null ? $popular : '',
], fn($v) => $v !== '');
$qs = http_build_query($qsArray);

include 'includes/header.php';
include 'includes/sidebar.php';

$reopenModal = $_SESSION['reopen_modal'] ?? '';
unset($_SESSION['reopen_modal']);
?>
<link rel="stylesheet" href="assets/css/admin-crud.css">

<div class="admin-content" data-reopen-modal="<?= htmlspecialchars($reopenModal) ?>">
  <h2>Tours</h2>

  <?php include 'includes/admin-alert.php'; ?>

  <div class="crud-toolbar">
    <form class="toolbar-filters" method="GET">
      <div class="search-input">
        <input type="text" name="search" placeholder="Search title / location..." value="<?= htmlspecialchars($search) ?>">
      </div>

      <select name="type" class="auto-submit">
        <option value="">All Types</option>
        <option value="domestic" <?= $type === 'domestic' ? 'selected' : '' ?>>Domestic</option>
        <option value="international" <?= $type === 'international' ? 'selected' : '' ?>>International</option>
      </select>

      <select name="popular" class="auto-submit">
        <option value="">Popular?</option>
        <option value="1" <?= $popular === 1 ? 'selected' : '' ?>>Yes</option>
        <option value="0" <?= $popular === 0 ? 'selected' : '' ?>>No</option>
      </select>

      <select name="status" class="auto-submit">
        <option value="">All Status</option>
        <option value="1" <?= $status === 1 ? 'selected' : '' ?>>Active</option>
        <option value="0" <?= $status === 0 ? 'selected' : '' ?>>Inactive</option>
      </select>

      <button type="submit" class="btn-filter"><i class="fa-solid fa-filter"></i> Filter</button>
      <?php if ($search !== '' || $status !== null || $type !== '' || $popular !== null): ?>
        <a href="manage-tours" class="btn-reset">Reset</a>
      <?php endif; ?>
    </form>

    <button type="button" class="btn-add" onclick="openAddModal()"><i class="fa-solid fa-plus"></i> Add Tour</button>
  </div>

  <p class="result-count"><?= $totalRows ?> tour<?= $totalRows == 1 ? '' : 's' ?> found</p>

  <div class="table-scroll">
    <table class="admin-table">
      <thead>
        <tr>
          <th>S.N.</th>
          <th>Created Date</th>
          <th>Title</th>
          <th>Type</th>
          <th>Duration</th>
          <th>Price</th>
          <th>Price USD</th>
          <th>Overview</th>
          <th>Itinerary</th>
          <th>Image</th>
          <th>PDF</th>
          <th>Location</th>
          <th>Popular</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php $i = $offset + 1; ?>
        <?php if ($totalRows == 0): ?>
          <tr>
            <td colspan="15" class="no-results">No tours match your search/filter.</td>
          </tr>
        <?php endif; ?>
        <?php while ($row = $result->fetch_assoc()):
          $itRes = mysqli_query($conn, "SELECT day_number, title, description FROM tour_itineraries WHERE tour_id = {$row['id']} ORDER BY day_number ASC");
          $itinerary = [];
          while ($it = mysqli_fetch_assoc($itRes)) $itinerary[] = $it;
        ?>
          <tr>
            <td><?= $i++ ?></td>
            <td><?= htmlspecialchars($row['created_at']) ?></td>
            <td><?= htmlspecialchars($row['title']) ?></td>
            <td><?= htmlspecialchars($row['type']) ?></td>
            <td><?= htmlspecialchars($row['duration']) ?></td>
            <td><?= htmlspecialchars($row['price']) ?></td>
            <td><?= htmlspecialchars($row['price_usd']) ?></td>
            <td><?= implode(' ', array_slice(explode(' ', $row['overview']), 0, 5)) ?>...</td>
            <td>
              <?php if ($itinerary): ?>
                <ul style="padding-left:15px; margin:0;">
                  <?php foreach (array_slice($itinerary, 0, 2) as $it): ?>
                    <li><strong>Day <?= $it['day_number'] ?>:</strong> <?= htmlspecialchars($it['title']) ?></li>
                  <?php endforeach; ?>
                </ul>
                <small style="color:#777;">+ more</small>
              <?php else: ?>
                <em>No itinerary</em>
              <?php endif; ?>
            </td>
            <td><img src="../uploads/images/tours/<?= htmlspecialchars($row['banner_image']) ?>" height="50"></td>
            <td><?php if ($row['pdf_file']): ?><a href="../uploads/pdf/<?= htmlspecialchars($row['pdf_file']) ?>" target="_blank" class="btn-view">View</a><?php endif; ?></td>
            <td><?= htmlspecialchars($row['location_name']) ?></td>
            <td><?= $row['is_popular'] ? '<span class="badge badge-popular">Yes</span>' : 'No'; ?></td>
            <td class="status-col"><span class="pill <?= $row['status'] ? 'published' : 'draft' ?>"><?= $row['status'] ? 'Active' : 'Inactive' ?></span></td>
            <td class="action-col">
              <button type="button" class="btn-edit"
                onclick="openEditModal(this)"
                data-id="<?= $row['id'] ?>"
                data-title="<?= htmlspecialchars($row['title']) ?>"
                data-type="<?= htmlspecialchars($row['type']) ?>"
                data-duration="<?= htmlspecialchars($row['duration']) ?>"
                data-price="<?= htmlspecialchars($row['price']) ?>"
                data-price_usd="<?= htmlspecialchars($row['price_usd']) ?>"
                data-overview="<?= htmlspecialchars($row['overview']) ?>"
                data-highlights="<?= htmlspecialchars($row['highlights']) ?>"
                data-includes="<?= htmlspecialchars($row['includes']) ?>"
                data-excludes="<?= htmlspecialchars($row['excludes']) ?>"
                data-latitude="<?= htmlspecialchars($row['latitude']) ?>"
                data-longitude="<?= htmlspecialchars($row['longitude']) ?>"
                data-location_name="<?= htmlspecialchars($row['location_name']) ?>"
                data-is_popular="<?= $row['is_popular'] ?>"
                data-status="<?= $row['status'] ?>"
                data-image="<?= htmlspecialchars($row['banner_image']) ?>"
                data-image-path="../uploads/images/tours/<?= htmlspecialchars($row['banner_image']) ?>"
                data-itinerary='<?= htmlspecialchars(json_encode($itinerary), ENT_QUOTES) ?>'>
                Edit
              </button>
              <a href="javascript:void(0)"
                onclick="showConfirm('?delete=<?= $row['id'] ?>&qs=<?= urlencode($qs) ?>','Delete this package?')"
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
  <div class="crud-modal large">
    <button type="button" class="modal-close" onclick="closeModal('addModal')">&times;</button>
    <h2>Add New Tour</h2>
    <form method="POST" enctype="multipart/form-data" class="admin-form validate-form">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
      <input type="hidden" name="qs" value="<?= htmlspecialchars($qs) ?>">

      <div class="form-group"><input type="text" name="title" id="add-title" placeholder="Tour Title" data-validate="text10"><small class="error"></small></div>
      <div class="form-group"><input type="text" name="slug" id="add-slug" placeholder="Slug (auto-generated)" data-validate="text10"><small class="error"></small></div>

      <label>Type</label>
      <select name="type">
        <option value="domestic">Domestic</option>
        <option value="international">International</option>
      </select>

      <div class="form-group"><input type="text" name="duration" placeholder="Duration (e.g. 7 Days)"><small class="error"></small></div>
      <div class="form-group"><input type="number" step="0.01" name="price" placeholder="Price in NPR"><small class="error"></small></div>
      <div class="form-group"><input type="number" step="0.01" name="price_usd" placeholder="Price in USD"><small class="error"></small></div>
      <div class="form-group"><input type="number" step="0.01" name="old_price" placeholder="Old Price in NPR"><small class="error"></small></div>
      <div class="form-group"><textarea name="overview" placeholder="Trip Overview" data-validate="text20"></textarea><small class="error"></small></div>
      <div class="form-group"><textarea name="highlights" placeholder="Trip Highlights (one per line)"></textarea><small class="error"></small></div>

      <label>Itinerary</label>
      <div id="itinerary-wrapper">
        <div class="itinerary-row">
          <div class="form-group"><input type="number" name="day_no[]" placeholder="Day 1" class="day-no"><small class="error"></small></div>
          <div class="form-group"><input type="text" name="itinerary_title[]" placeholder="Title" class="it-title"><small class="error"></small></div>
          <div class="form-group"><textarea name="itinerary_desc[]" placeholder="Description" class="it-desc"></textarea><small class="error"></small></div>
          <button type="button" class="remove-itinerary">Remove</button>
        </div>
      </div>
      <button type="button" class="additinerarybtn" onclick="addItinerary('itinerary-wrapper')">+ Add Day</button>

      <div class="form-group"><textarea name="includes" placeholder="Cost Includes"></textarea></div>
      <div class="form-group"><textarea name="excludes" placeholder="Cost Excludes"></textarea></div>

      <div class="file_input"><label>Banner Image *</label><input type="file" name="banner" accept="image/*" required></div>
      <div class="file_input"><label>Trip PDF</label><input type="file" name="pdf" accept="application/pdf"></div>

      <div class="form-group"><input type="number" step="any" name="latitude" placeholder="Latitude"></div>
      <div class="form-group"><input type="number" step="any" name="longitude" placeholder="Longitude"></div>
      <div class="form-group"><input type="text" name="location_name" placeholder="Location Name"></div>

      <label>Is Popular?</label>
      <select name="is_popular">
        <option value="0">No</option>
        <option value="1">Yes</option>
      </select>

      <label>Status</label>
      <select name="status" required>
        <option value="1">Active</option>
        <option value="0">Inactive</option>
      </select>

      <button name="submit">Add Tour</button>
    </form>
  </div>
</div>

<!-- EDIT MODAL -->
<div class="crud-modal-overlay" id="editModal">
  <div class="crud-modal large">
    <button type="button" class="modal-close" onclick="closeModal('editModal')">&times;</button>
    <h2>Edit Tour</h2>
    <form method="POST" enctype="multipart/form-data" class="admin-form validate-form">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
      <input type="hidden" name="qs" value="<?= htmlspecialchars($qs) ?>">
      <input type="hidden" name="id" value="">

      <div class="form-group"><input type="text" name="title" placeholder="Tour Title" data-validate="text10"><small class="error"></small></div>

      <label>Type</label>
      <select name="type">
        <option value="domestic">Domestic</option>
        <option value="international">International</option>
      </select>

      <div class="form-group"><input type="text" name="duration" placeholder="Duration"></div>
      <div class="form-group"><input type="number" step="0.01" name="price" placeholder="Price in NPR"></div>
      <div class="form-group"><input type="number" step="0.01" name="price_usd" placeholder="Price in USD"></div>
      <div class="form-group"><textarea name="overview" data-validate="text20"></textarea><small class="error"></small></div>
      <div class="form-group"><textarea name="highlights"></textarea></div>

      <label>Itinerary</label>
      <div id="edit-itinerary-wrapper">
        <div class="itinerary-row">
          <div class="form-group"><input type="number" name="day_no[]" placeholder="Day 1" class="day-no"><small class="error"></small></div>
          <div class="form-group"><input type="text" name="itinerary_title[]" placeholder="Title" class="it-title"><small class="error"></small></div>
          <div class="form-group"><textarea name="itinerary_desc[]" placeholder="Description" class="it-desc"></textarea><small class="error"></small></div>
          <button type="button" class="remove-itinerary">Remove</button>
        </div>
      </div>
      <button type="button" class="additinerarybtn" onclick="addItinerary('edit-itinerary-wrapper')">+ Add Day</button>

      <div class="form-group"><textarea name="includes"></textarea></div>
      <div class="form-group"><textarea name="excludes"></textarea></div>

      <label>Current Image</label>
      <div class="current-image"><img src="" width="120" alt=""></div>
      <div class="file_input"><label>Change Banner Image</label><input type="file" name="banner" accept="image/*"></div>
      <div class="file_input"><label>Change Trip PDF</label><input type="file" name="pdf" accept="application/pdf"></div>

      <div class="form-group"><input type="number" step="any" name="latitude" placeholder="Latitude"></div>
      <div class="form-group"><input type="number" step="any" name="longitude" placeholder="Longitude"></div>
      <div class="form-group"><input type="text" name="location_name" placeholder="Location Name"></div>

      <label>Is Popular?</label>
      <select name="is_popular">
        <option value="0">No</option>
        <option value="1">Yes</option>
      </select>

      <label>Status</label>
      <select name="status">
        <option value="1">Active</option>
        <option value="0">Inactive</option>
      </select>

      <button name="update">Update Tour</button>
    </form>
  </div>
</div>

<script src="assets/js/itinerary-days-add-remove.js"></script>
<script src="assets/js/form-validator.js"></script>
<script src="assets/js/itinerary-validation.js"></script>
<script src="assets/js/admin-alert.js"></script>
<script src="assets/js/admin-crud.js"></script>

<script>
  const nameInput = document.getElementById('add-title');
  const slugInput = document.getElementById('add-slug');
  let slugEdited = false;

  function slugify(text) {
    return text.toString().trim().toLowerCase()
      .replace(/[^a-z0-9\s-]/g, '')
      .replace(/\s+/g, '-')
      .replace(/-+/g, '-')
      .replace(/^-|-$/g, '');
  }

  if (nameInput && slugInput) {
    nameInput.addEventListener('input', () => {
      if (!slugEdited) slugInput.value = slugify(nameInput.value);
    });
    slugInput.addEventListener('input', () => {
      slugEdited = true;
      slugInput.value = slugify(slugInput.value);
    });
  }
</script>

<?php include 'includes/footer.php'; ?>
<script src="../assets/js/confirmation.js"></script>