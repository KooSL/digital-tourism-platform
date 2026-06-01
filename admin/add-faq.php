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

    $q = $_POST['question'];
    $a = $_POST['answer'];
    $f = (int)$_POST['featured'];
    $s = (int)$_POST['status'];

    $stmt = mysqli_prepare($conn, "INSERT INTO faqs (question, answer, is_featured, status) VALUES (?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "ssii", $q, $a, $f, $s);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    $_SESSION['success'] = "FAQ added successfully.";
    header("Location: manage-faqs");
    exit();
}
?>

<div class="admin-content">
    <h2>Add FAQ</h2>

    <form method="POST" enctype="multipart/form-data" class="admin-form validate-form">

        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

        <div class="form-group">
            <input type="text" name="question" placeholder="Question" data-validate="text10">
            <small class="error"></small>
        </div>

        <div class="form-group">
            <textarea name="answer" placeholder="Answer" data-validate="text10"></textarea>
            <small class="error"></small>
        </div>

        <label>Featured</label>
        <select name="featured" required>
            <option value="0">No</option>
            <option value="1">Yes</option>
        </select>

        <label>Status</label>
        <select name="status" required>
            <option value="1">Active</option>
            <option value="0">Inactive</option>
        </select>

        <button name="submit">Add FAQ</button>
    </form>

    <script src="assets/js/form-validator.js"></script>
    <?php include 'includes/footer.php'; ?>