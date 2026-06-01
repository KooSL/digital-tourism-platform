<?php
include '../config/db.php';
include 'auth.php';
include 'includes/header.php';
include 'includes/sidebar.php';

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if (isset($_POST['submit'])) {

    if (
        !isset($_POST['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
    ) {
        die("CSRF validation failed.");
    }

    $from   = $_POST['from_city'];
    $to     = $_POST['to_city'];
    $desc   = $_POST['description'];
    $group_fare  = $_POST['group_fare'];
    $status = $_POST['status'];

    /* IMAGE UPLOAD */
    $imageName = null;

    if (!empty($_FILES['image']['name'])) {
        $imageName = time() . '_' . basename($_FILES['image']['name']);
        move_uploaded_file(
            $_FILES['image']['tmp_name'],
            "uploads/images/flights/" . $imageName
        );
    }

    /* PREPARED STATEMENT */
    $stmt = $conn->prepare(
        "INSERT INTO flights 
        (from_city, to_city, description, image, is_group_fare, status) 
        VALUES (?, ?, ?, ?, ?, ?)"
    );

    $stmt->bind_param(
        "ssssii",
        $from,
        $to,
        $desc,
        $imageName,
        $group_fare,
        $status
    );

    if ($stmt->execute()) {
        $_SESSION['success'] = "Flight added successfully.";
        header("Location: manage-flights");
        exit();
    } else {
        $_SESSION['error'] = "Failed to add flight.";
        header("Location: add-flight-post");
        exit();
    }

    $stmt->close();
}
?>

<div class="admin-content">
    <h2>Add New Flight Post</h2>

    <?php include 'includes/admin-alert.php'; ?>

    <form method="POST" enctype="multipart/form-data" class="admin-form validate-form">

        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

        <div class="form-group">
            <input type="text" name="from_city" id="from_city" placeholder="From City" data-validate="city">
            <small class="error"></small>
        </div>

        <div class="form-group">
            <input type="text" name="to_city" id="to_city" placeholder="To City"
                data-validate="city">
            <small class="error"></small>
        </div>

        <!-- <input type="text" name="price" placeholder="Starting Price"> -->

        <div class="form-group">
            <textarea name="description" id="description" placeholder="Description" data-validate="text20"></textarea>
            <small class="error"></small>
        </div>

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

<script src="assets/js/form-validator.js"></script>
<script src="assets/js/admin-alert.js"></script>

<?php include 'includes/footer.php'; ?>
