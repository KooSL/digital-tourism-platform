<?php
include 'auth.php';
include 'includes/header.php';
include 'includes/sidebar.php';
include '../config/db.php';

// CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Get ID safely
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    die("Invalid Bus ID.");
}

// Fetch bus data
$stmt = $conn->prepare("SELECT * FROM buses WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();
$stmt->close();

if (!$data) {
    die("Bus not found.");
}

$error = '';

// Handle update
if (isset($_POST['update'])) {

    // CSRF check
    if (
        !isset($_POST['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
    ) {
        die("CSRF validation failed.");
    }

    // Sanitize inputs
    $bus_name = trim($_POST['bus_name']);
    $bus_number = trim($_POST['bus_number']);
    $from = trim($_POST['from_location']);
    $to = trim($_POST['to_location']);
    $travel_date = $_POST['travel_date'];
    $departure_time = $_POST['departure_time'];
    $arrival_time = $_POST['arrival_time'];
    $price = (int)$_POST['price'];
    $total_seats = (int)$_POST['total_seats'];
    $desc = trim($_POST['description']);
    $status = (int)$_POST['status'];

    // Keep old image
    $image = $data['banner_image'];
    $upload_error = '';

    // Image upload
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
            $path = "uploads/images/buses/" . $newImage;

            if (move_uploaded_file($tmp, $path)) {

                // delete old image
                $old = "uploads/images/buses/" . $data['banner_image'];
                if (!empty($data['banner_image']) && file_exists($old)) {
                    unlink($old);
                }

                $image = $newImage;
            } else {
                $upload_error = "Upload failed.";
            }
        }
    }

    if (empty($error) && empty($upload_error)) {

        $stmt = $conn->prepare("UPDATE buses SET 
            bus_name=?, 
            bus_number=?, 
            from_location=?, 
            to_location=?, 
            travel_date=?, 
            departure_time=?, 
            arrival_time=?, 
            price=?, 
            total_seats=?, 
            description=?, 
            banner_image=?, 
            status=? 
            WHERE id=?");

        $stmt->bind_param(
            "sssssssisssii",
            $bus_name,
            $bus_number,
            $from,
            $to,
            $travel_date,
            $departure_time,
            $arrival_time,
            $price,
            $total_seats,
            $desc,
            $image,
            $status,
            $id
        );

        if ($stmt->execute()) {
            header("Location: manage-buses");
            exit;
        } else {
            $error = "Update failed.";
        }

        $stmt->close();
    } else {
        $error = $upload_error ?: $error;
    }
}
?>

<div class="admin-content">
    <h2>Edit Bus</h2>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="admin-form validate-form">

        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

        <div class="form-group">
            <input type="text" name="bus_name" placeholder="Bus Name"
                value="<?= htmlspecialchars($data['bus_name']) ?>" data-validate="name">
            <small class="error"></small>
        </div>

        <div class="form-group">
            <input type="text" name="bus_number" placeholder="Bus Number"
                value="<?= htmlspecialchars($data['bus_number']) ?>">
            <small class="error"></small>
        </div>

        <div class="form-group">
            <input type="text" name="from_location" placeholder="From"
                value="<?= htmlspecialchars($data['from_location']) ?>" data-validate="city">
            <small class="error"></small>
        </div>

        <div class="form-group">
            <input type="text" name="to_location" placeholder="To"
                value="<?= htmlspecialchars($data['to_location']) ?>" data-validate="city">
            <small class="error"></small>
        </div>

        <div class="form-group">
            <input type="date" name="travel_date"
                value="<?= htmlspecialchars($data['travel_date']) ?>" data-validate="date">
            <small class="error"></small>
        </div>

        <div class="form-group">
            <input type="text" name="departure_time"
                value="<?= htmlspecialchars($data['departure_time']) ?>" data-validate="time">
            <small class="error"></small>
        </div>

        <div class="form-group">
            <input type="text" name="arrival_time"
                value="<?= htmlspecialchars($data['arrival_time']) ?>" data-validate="time">
            <small class="error"></small>
        </div>

        <div class="form-group">
            <input type="number" name="price"
                value="<?= htmlspecialchars($data['price']) ?>" data-validate="number">
            <small class="error"></small>
        </div>

        <div class="form-group">
            <input type="number" name="total_seats"
                value="<?= htmlspecialchars($data['total_seats']) ?>" data-validate="number">
            <small class="error"></small>
        </div>

        <div class="form-group">
            <textarea name="description" data-validate="text20"><?= htmlspecialchars($data['description']) ?></textarea>
            <small class="error"></small>
        </div>

        <label>Current Image</label>
        <div class="current-image">
            <img src="uploads/images/buses/<?= htmlspecialchars($data['banner_image']) ?>" width="120">
        </div>

        <div class="file_input">
            <label>Change Image</label>
            <input type="file" name="image">
        </div>

        <label>Status</label>
        <select name="status">
            <option value="1" <?= $data['status'] == 1 ? 'selected' : '' ?>>Active</option>
            <option value="0" <?= $data['status'] == 0 ? 'selected' : '' ?>>Inactive</option>
        </select>

        <button type="submit" name="update">Update</button>
    </form>
</div>
<script src="assets/js/form-validator.js"></script>
<?php include 'includes/footer.php'; ?>