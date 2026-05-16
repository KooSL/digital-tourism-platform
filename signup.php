<?php include 'includes/header.php'; ?>

<?php

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

require_once 'config/db.php';
require_once 'includes/mailer.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['signup'])) {

    if (
        !isset($_POST['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
    ) {
        die("CSRF validation failed.");
    }

    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($name) || empty($email) || empty($phone) || empty($password)) {
        die("All fields are required");
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email format");
    }

    if (!preg_match('/^[0-9]{7,15}$/', $phone)) {
        die("Invalid phone number");
    }

    if ($password !== $confirm_password) {
        die("Passwords do not match");
    }

    if (strlen($password) < 8) {
        die("Password must be at least 8 characters");
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $_SESSION['error'] = "Email already exists";
        header("Location: signup?error=1");
        exit;
    }

    $stmt = $conn->prepare("
        INSERT INTO users (name, email, phone, password)
        VALUES (?, ?, ?, ?)
    ");

    $stmt->bind_param("ssss", $name, $email, $phone, $hashedPassword);

    if ($stmt->execute()) {

        $_SESSION['user_id'] = $stmt->insert_id;
        $_SESSION['user_name'] = $name;
        $_SESSION['success'] = "Registration successful! Welcome, $name.";

        header("Location: index?success=1");
        exit;
    } else {
        $_SESSION['error'] = "Registration failed! Please try again.";
        header("Location: signup?error=1");
        exit;
    }
}
?>

<div class="header-wrapper">
    <?php include 'includes/topbar.php'; ?>
    <?php include 'includes/navbar.php'; ?>
</div>

<section class="page-banner">

    <?php if (isset($_GET['success'])): ?>
        <div class="success-box" id="successBox">
            <strong>Success!</strong>
            <p><?php echo isset($_SESSION['success']) ? $_SESSION['success'] : 'Registration successful!'; ?></p>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="error-box" id="errorBox">
            <strong>Error!</strong>
            <p><?php echo isset($_SESSION['error']) ? $_SESSION['error'] : 'Something went wrong!'; ?></p>
        </div>
    <?php endif; ?>

    <div class="overlay">
        <h1>Sign Up</h1>
        <p>Join us to access exclusive travel deals and personalized services.</p>
    </div>
</section>

<div class="contact-form-box">
    <div class="container">

        <form method="POST" id="registerForm" novalidate>

            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

            <div class="form-group">
                <input type="text" name="name" id="name" placeholder="Full Name">
                <small class="error"></small>
            </div>

            <div class="form-group">
                <input type="email" name="email" id="email" placeholder="Email">
                <small class="error"></small>
            </div>

            <div class="form-group">
                <input type="text" name="phone" id="phone" placeholder="Phone Number">
                <small class="error"></small>
            </div>

            <div class="form-group">
                <input type="password" name="password" id="password" placeholder="Password">
                <small class="error"></small>
            </div>

            <div class="form-group">
                <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm Password">
                <small class="error"></small>
            </div>

            <p class="acc-sign-txt">Already have an account? <a href="signin">Sign In</a></p>

            <button type="submit" name="signup">Sign Up</button>
        </form>
    </div>
</div>

<script src="assets/js/auth-validation.js"></script>
<script src="assets/js/success-errorBox.js"></script>

<?php include 'includes/footer.php'; ?>