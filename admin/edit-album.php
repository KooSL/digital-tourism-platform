<?php
include 'auth.php';
include 'includes/header.php';
include 'includes/sidebar.php';
include '../config/db.php';

// Generate CSRF token if not exists
if (empty($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Initialize error array for validation messages
$errors = [];

// Validate and sanitize ID
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
  die("Invalid album ID.");
}

// Use prepared statement to fetch album data
$stmt = $conn->prepare("SELECT * FROM gallery_albums WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
  die("Album not found.");
}

$data = $result->fetch_assoc();
$stmt->close();

$oldSlug = $data['slug'];

// Initialize $newSlug for form display
$newSlug = $oldSlug;

// Store old values for form redisplay
$oldTitle = $data['title'];
$oldStatus = $data['status'];

if (isset($_POST['update'])) {

  // CSRF validation
  if (
    !isset($_POST['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
  ) {
    $errors[] = "CSRF validation failed.";
  } else {
    // Regenerate CSRF token after successful validation
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
  }

  // Validate title
  $title = trim($_POST['title'] ?? '');
  if (empty($title)) {
    $errors[] = "Title is required.";
  } elseif (strlen($title) < 2) {
    $errors[] = "Title must be at least 2 characters.";
  } elseif (strlen($title) > 255) {
    $errors[] = "Title must not exceed 255 characters.";
  }

  // Generate slug from title
  $newSlug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));

  // Validate slug to prevent path traversal
  if (preg_match('/\.\./', $newSlug) || preg_match('/^[-\/]/', $newSlug) || preg_match('/[-\/]$/', $newSlug)) {
    $errors[] = "Invalid slug format.";
  }

  // Validate status
  $status = isset($_POST['status']) ? (int)$_POST['status'] : -1;
  if (!in_array($status, [0, 1], true)) {
    $errors[] = "Invalid status value.";
  }

  // If no errors, proceed with update
  if (empty($errors)) {

    // Use prepared statement for update
    $cover = $data['cover_image'];
    $oldPath = "uploads/gallery/" . $oldSlug;
    $newPath = "uploads/gallery/" . $newSlug;

    /* -----------------------------
       RENAME FOLDER IF SLUG CHANGED
    ------------------------------*/
    if ($oldSlug !== $newSlug) {

      // Validate paths to prevent directory traversal
      $oldPath = realpath($oldPath);
      $basePath = realpath("uploads/gallery");
      
      // Ensure paths are within allowed directory
      if ($oldPath !== false && $basePath !== false && strpos($oldPath, $basePath) === 0) {
        if (is_dir($oldPath)) {
          rename($oldPath, $newPath);
        } else {
          mkdir($newPath, 0777, true);
        }
      }
    }

    /* -----------------------------
       HANDLE COVER IMAGE UPDATE
    ------------------------------*/
    if (!empty($_FILES['cover']['name'])) {

      // File upload validation
      $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
      $maxFileSize = 2 * 1024 * 1024; // 2MB

      $fileName = $_FILES['cover']['name'];
      $fileSize = $_FILES['cover']['size'];
      $fileTmpName = $_FILES['cover']['tmp_name'];
      $fileType = mime_content_type($fileTmpName);

      // Validate file type
      if (!in_array($fileType, $allowedTypes, true)) {
        $errors[] = "Invalid file type. Only JPEG, PNG, GIF, and WebP are allowed.";
      }
      // Validate file size
      elseif ($fileSize > $maxFileSize) {
        $errors[] = "File size must not exceed 2MB.";
      }
      else {
        // Get safe filename
        $newCover = time() . '_' . preg_replace('/[^A-Za-z0-9._-]/', '_', $fileName);

        // Validate destination path
        $newPathReal = realpath("uploads/gallery/" . $newSlug);
        $basePath = realpath("uploads/gallery");
        
        if ($newPathReal === false) {
          $newPathReal = "uploads/gallery/" . $newSlug;
        }

        // Ensure the path is within allowed directory
        if ($basePath !== false && strpos(realpath(dirname($newPathReal . '/' . $newCover)), $basePath) === 0) {
          if (move_uploaded_file($fileTmpName, $newPath . '/' . $newCover)) {

            // delete old cover image
            if (!empty($data['cover_image'])) {
              $oldCoverPath = $newPath . '/' . $data['cover_image'];
              if (file_exists($oldCoverPath) && is_file($oldCoverPath)) {
                unlink($oldCoverPath);
              }
            }

            $cover = $newCover;
          } else {
            $errors[] = "Failed to upload file.";
          }
        } else {
          $errors[] = "Invalid upload path.";
        }
      }
    }

    // If still no errors, update database
    if (empty($errors)) {

      // Use prepared statement for database update
      $updateStmt = $conn->prepare("UPDATE gallery_albums SET title = ?, slug = ?, cover_image = ?, status = ? WHERE id = ?");
      $updateStmt->bind_param("sssii", $title, $newSlug, $cover, $status, $id);

      if ($updateStmt->execute()) {
        $updateStmt->close();
        header("Location: manage-albums");
        exit;
      } else {
        $errors[] = "Database update failed: " . $conn->error;
      }
      $updateStmt->close();
    }
  }
}
?>


<div class="admin-content">
  <h2>Edit Album</h2>

  <?php if (!empty($errors)): ?>
    <div class="error-messages">
      <?php foreach ($errors as $error): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <form method="POST" enctype="multipart/form-data" class="admin-form validate-form">

    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">

    <div class="form-group">
      <input type="text" name="title" value="<?= htmlspecialchars($title ?? $oldTitle, ENT_QUOTES, 'UTF-8') ?>" required data-validate="name" minlength="2" maxlength="255">
      <small class="error"></small>
    </div>

    <label>Current Cover Image</label>
    <div class="current-image">
      <?php if (!empty($data['cover_image'])): ?>
        <img src="uploads/gallery/<?= htmlspecialchars($newSlug, ENT_QUOTES, 'UTF-8') ?>/<?= htmlspecialchars($data['cover_image'], ENT_QUOTES, 'UTF-8') ?>" alt="Cover Image">
      <?php else: ?>
        <p>No cover image</p>
      <?php endif; ?>
    </div>

    <div class="file_input">
      <label>Change Cover Image</label>
      <input type="file" name="cover" accept="image/jpeg,image/png,image/gif,image/webp">
      <small class="error">Max size: 2MB. Allowed: JPEG, PNG, GIF, WebP</small>
    </div>

    <label>Status</label>
    <select name="status">
      <option value="1" <?= (($status ?? $oldStatus) == 1) ? 'selected' : '' ?>>Active</option>
      <option value="0" <?= (($status ?? $oldStatus) == 0) ? 'selected' : '' ?>>Inactive</option>
    </select>

    <button type="submit" name="update">Update Album</button>
  </form>
</div>

<script src="assets/js/form-validator.js"></script>
<?php include 'includes/footer.php'; ?>
