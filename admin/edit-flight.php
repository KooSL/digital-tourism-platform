<?php
include 'auth.php';
include 'includes/header.php';
include 'includes/sidebar.php';
include '../config/db.php';

// Generate CSRF token if not exists
if (empty($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Validate and sanitize ID parameter
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
  die("Invalid flight ID.");
}

// Use prepared statement to prevent SQL injection
$stmt = $conn->prepare("SELECT * FROM flights WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();
$stmt->close();

// Check if flight exists
if (!$data) {
  die("Flight not found.");
}

// Initialize variables for form
$error = '';
$success = '';

// Handle form submission
if (isset($_POST['update'])) {

  // CSRF validation
  if (
    !isset($_POST['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
  ) {
    die("CSRF validation failed.");
  }

  // Input Validation and Sanitization
  $from_city = isset($_POST['from_city']) ? trim($_POST['from_city']) : '';
  $to_city = isset($_POST['to_city']) ? trim($_POST['to_city']) : '';
  $description = isset($_POST['description']) ? trim($_POST['description']) : '';
  $group_fare = isset($_POST['group_fare']) ? (int)$_POST['group_fare'] : 0;
  $status = isset($_POST['status']) ? (int)$_POST['status'] : 0;

  // Validate required fields
  if (empty($from_city)) {
    $error = "From City is required.";
  } elseif (strlen($from_city) > 100) {
    $error = "From City must be less than 100 characters.";
  } elseif (empty($to_city)) {
    $error = "To City is required.";
  } elseif (strlen($to_city) > 100) {
    $error = "To City must be less than 100 characters.";
  } elseif ($from_city === $to_city) {
    $error = "From City and To City cannot be the same.";
  }

  // Validate group_fare and status are valid integers
  if (!in_array($group_fare, [0, 1], true)) {
    $group_fare = 0;
  }
  
  if (!in_array($status, [0, 1], true)) {
    $status = 0;
  }

  // If no validation errors, proceed with update
  if (empty($error)) {
    
    // Keep existing image
    $image = $data['image'];
    $upload_error = '';

    // Handle file upload if new image is provided
    if (!empty($_FILES['image']['name'])) {
      
      // File upload validation
      $max_file_size = 2 * 1024 * 1024; // 2MB
      $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
      $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
      
      $file_size = $_FILES['image']['size'];
      $file_tmp_name = $_FILES['image']['tmp_name'];
      $file_name = $_FILES['image']['name'];
      $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
      $file_mime = mime_content_type($file_tmp_name);

      // Validate file size
      if ($file_size > $max_file_size) {
        $upload_error = "File size must be less than 2MB.";
      }
      // Validate MIME type
      elseif (!in_array($file_mime, $allowed_types, true)) {
        $upload_error = "Invalid file type. Only JPEG, PNG, GIF, and WebP are allowed.";
      }
      // Validate extension
      elseif (!in_array($file_ext, $allowed_extensions, true)) {
        $upload_error = "Invalid file extension.";
      }
      // Validate actual image content
      elseif (!getimagesize($file_tmp_name)) {
        $upload_error = "File is not a valid image.";
      }
      else {
        // Generate safe filename with random string
        $newImage = bin2hex(random_bytes(8)) . '_' . time() . '.' . $file_ext;
        $upload_path = "uploads/images/flights/" . $newImage;

        if (move_uploaded_file($file_tmp_name, $upload_path)) {
          // Delete old image with proper error handling
          $old_image_path = "uploads/images/flights/" . $data['image'];
          if (!empty($data['image']) && file_exists($old_image_path) && is_file($old_image_path)) {
            if (!unlink($old_image_path)) {
              error_log("Failed to delete old image: " . $old_image_path);
            }
          }
          $image = $newImage;
        } else {
          $upload_error = "Failed to upload image.";
        }
      }
    }

    if (!empty($upload_error)) {
      $error = $upload_error;
    } else {
      // Use prepared statement for UPDATE query
      $update_stmt = $conn->prepare("UPDATE flights SET from_city = ?, to_city = ?, description = ?, image = ?, is_group_fare = ?, status = ? WHERE id = ?");
      $update_stmt->bind_param("sssssii", $from_city, $to_city, $description, $image, $group_fare, $status, $id);
      
      if ($update_stmt->execute()) {
        $update_stmt->close();
        header("Location: manage-flights");
        exit;
      } else {
        $error = "Failed to update flight. Please try again.";
        $update_stmt->close();
      }
    }
  }
}
?>

<div class="admin-content">
  <h2>Edit Flight Post</h2>

  <?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
  <?php endif; ?>

  <?php if (!empty($success)): ?>
    <div class="alert alert-success"><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></div>
  <?php endif; ?>

  <form method="POST" enctype="multipart/form-data" class="admin-form validate-form">

    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">

    <div class="form-group">
      <input type="text" name="from_city" placeholder="From City" value="<?= htmlspecialchars($data['from_city'] ?? '', ENT_QUOTES, 'UTF-8') ?>" data-validate="city" required>
      <small class="error"></small>
    </div>

    <div class="form-group">
      <input type="text" name="to_city" placeholder="To City" value="<?= htmlspecialchars($data['to_city'] ?? '', ENT_QUOTES, 'UTF-8') ?>" data-validate="city" required>
      <small class="error"></small>
    </div>

    <div class="form-group">
      <textarea name="description" placeholder="Description" data-validate="text20"><?= htmlspecialchars($data['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
      <small class="error"></small>
    </div>


    <label>Current Image</label>
    <div class="current-image">
      <img src="uploads/images/flights/<?= htmlspecialchars($data['image'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" alt="Current Flight Image">
    </div>

    <div class="file_input">
      <label>Change Image (optional)</label>
      <input type="file" name="image" accept="image/jpeg,image/png,image/gif,image/webp">
      <small class="error">Max size: 2MB. Allowed: JPEG, PNG, GIF, WebP</small>
    </div>

    <label>Group Fare</label>
    <select name="group_fare">
      <option value="1" <?= (isset($data['is_group_fare']) && $data['is_group_fare'] == 1) ? 'selected' : '' ?>>Yes</option>
      <option value="0" <?= (isset($data['is_group_fare']) && $data['is_group_fare'] == 0) ? 'selected' : '' ?>>No</option>
    </select>

    <label>Status</label>
    <select name="status">
      <option value="1" <?= (isset($data['status']) && $data['status'] == 1) ? 'selected' : '' ?>>Active</option>
      <option value="0" <?= (isset($data['status']) && $data['status'] == 0) ? 'selected' : '' ?>>Inactive</option>
    </select>

    <button type="submit" name="update">Update Post</button>
  </form>
</div>

<script src="assets/js/form-validator.js"></script>
<?php include 'includes/footer.php'; ?>
