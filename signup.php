<?php include 'includes/header.php'; ?>

<?php

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

require_once 'config/db.php';
require_once 'includes/mailer.php';
require_once 'api/countries.php';

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
    $address = trim($_POST['address']);
    $country = trim($_POST['country']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($name) || empty($email) || empty($phone) || empty($address) || empty($country) || empty($password)) {
        die("All fields are required");
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email format");
    }

    if (!preg_match('/^[0-9]{7,15}$/', $phone)) {
        die("Invalid phone number");
    }

    if (strlen($address) < 5) {
        die("Address must be at least 5 characters");
    }

    if ($password !== $confirm_password) {
        die("Passwords do not match");
    }

    if (strlen($password) < 8) {
        die("Password must be at least 8 characters");
    }

    if (!in_array($country, $countries)) {
        die("Invalid country selected");
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        header("Location: signup?error=email_exist");
        exit;
    }

    $otp = random_int(100000, 999999);

    $_SESSION['otp'] = $otp;
    $_SESSION['otp_expire'] = time() + 300;

    $_SESSION['signup_data'] = [
        'name' => $name,
        'email' => $email,
        'phone' => $phone,
        'address' => $address,
        'country' => $country,
        'password' => $hashedPassword
    ];

    sendOtpMail($email, $otp);

    header("Location: verify-otp");
    exit;
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
            <?php
            if ($_GET['success'] === 'signup') echo "Sign Up successful! Welcome, " . (isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'User') . ".";
            ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="error-box" id="errorBox">
            <strong>Error!</strong>
            <?php
            if ($_GET['error'] === 'email_exist') echo "Email already exists.";
            if ($_GET['error'] === 'invalid') echo "Registration failed! Please try again.";
            if ($_GET['error'] === 'otp_expired') echo "OTP has been expired! Please signup again.";
            ?>
        </div>
    <?php endif; ?>

    <div class="overlay">
        <h1>Sign Up</h1>
        <p>Join us to access exclusive travel deals and personalized services.</p>
    </div>
</section>

<div class="auth-container">
    <div class="auth-form">

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
                <select name="country" id="country">
                    <option value="" disabled selected>Select Country</option>
                    <?php foreach ($countries as $country): ?>
                        <option value="<?= htmlspecialchars($country) ?>">
                            <?= htmlspecialchars($country) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <small class="error"></small>
            </div>

            <div class="form-group">
                <input type="text" name="address" id="address" placeholder="Address">
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

            <button type="submit" name="signup" class="auth-btn">Sign Up</button>

            <p class="auth-switch">
                Already have an account?
                <a href="signin">Sign In</a>
            </p>
        </form>
    </div>
</div>

<script src="assets/js/auth-validation.js"></script>
<script src="assets/js/success-errorBox.js"></script>

<?php include 'includes/footer.php'; ?>