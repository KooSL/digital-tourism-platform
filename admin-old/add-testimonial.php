<?php
include '../config/db.php';
include 'auth.php';


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

    $name    = $_POST['name'];
    $service = $_POST['service'];
    $review  = $_POST['review'];
    $rating  = $_POST['rating'];
    $status  = $_POST['status'];

    $stmt = $conn->prepare("
        INSERT INTO testimonials (name, service, review, rating, status)
        VALUES (?, ?, ?, ?, ?)
    ");

    $stmt->bind_param("sssii", $name, $service, $review, $rating, $status);

    if ($stmt->execute()) {
        $stmt->close();
        $_SESSION['success'] = "Testimonial added successfully.";
        header("Location: manage-testimonials");
        exit();
    }
}


include 'includes/header.php';
include 'includes/sidebar.php';

?>


<div class="admin-content">
    <h2>Add Testimonial</h2>

    <?php include 'includes/admin-alert.php'; ?>

    <form method="POST" enctype="multipart/form-data" class="admin-form validate-form">

        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

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
            <option value="5">★★★★★</option>
            <option value="4">★★★★</option>
            <option value="3">★★★</option>
            <option value="2">★★</option>
            <option value="1">★</option>
        </select>

        <label>Status</label>
        <select name="status">
            <option value="1">Active</option>
            <option value="0">Inactive</option>
        </select>

        <button type="submit" name="submit">Add Testimonial</button>
    </form>
</div>

<script src="assets/js/form-validator.js"></script>
<script src="assets/js/admin-alert.js"></script>

<?php include 'includes/footer.php'; ?>