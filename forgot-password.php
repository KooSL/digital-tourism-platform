<?php 
$pageTitle = "Forgot Password";
include 'includes/header.php'; ?>

<?php

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

require_once 'config/db.php';
require_once 'includes/mailer.php';

$conn->query(
    "DELETE FROM password_resets 
     WHERE expires_at < NOW()"
);

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['send'])) {

    if (
        !isset($_POST['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
    ) {
        die("CSRF validation failed.");
    }

    $email = trim($_POST['email']);

    if (empty($email)) {
        die("Email is required");
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email format");
    }

    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if (!$user) {
        header("Location: forgot-password?error=email_doesnt_exist");
        exit;
    }

    // $otp = random_int(100000, 999999);

    // $_SESSION['otp'] = $otp;
    // $_SESSION['otp_expire'] = time() + 300;

    // $_SESSION['forgot_password_data'] = [
    //     'email' => $email,
    // ];

    // sendFPOtpMail($email, $otp);

    // header("Location: verify-otp");
    // exit;

    $token = bin2hex(random_bytes(32));

    $stmt = $conn->prepare(
        "INSERT INTO password_resets
        (user_id, token, expires_at)
        VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 10 MINUTE))"
    );

    $stmt->bind_param(
        "is",
        $user['id'],
        $token
    );

    $stmt->execute();

    $link = "http://localhost/Digital_Tourism_Platform/reset-password?token=" . $token;

    sendResetPWMail($email, $link);

    header("Location: forgot-password?success=link_sent");
    exit;
}
?>

<div class="header-wrapper">
    <?php include 'includes/topbar.php'; ?>
    <?php include 'includes/navbar.php'; ?>
</div>

<section class="page-banner">

    <?php if (isset($_GET['success'])): ?>
        <div class="success-box-contact" id="successBox">
            <strong>Success!</strong>
            <?php
            if ($_GET['success'] === 'signup') echo "Sign Up successful! Welcome, " . (isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'User') . ".";
            if ($_GET['success'] === 'link_sent') echo "Reset link has been sent to your email.";
            ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="error-box" id="errorBox">
            <strong>Error!</strong>
            <?php
            if ($_GET['error'] === 'email_doesnt_exist') echo "Account does not exist.";
            if ($_GET['error'] === 'invalid') echo "Registration failed! Please try again.";
            if ($_GET['error'] === 'otp_expired') echo "OTP has been expired! Please signup again.";
            if ($_GET['error'] === 'invalid_expired') echo "Link is invalid or has been expired! Please try again.";
            ?>
        </div>
    <?php endif; ?>

    <div class="overlay">
        <h1>Forgot Password?</h1>
        <p>Enter your email to get password reset link.</p>
    </div>
</section>

<div class="auth-container">
    <div class="auth-form">

        <form method="POST" id="registerForm" novalidate>

            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

            <div class="form-group">
                <input type="email" name="email" id="email" placeholder="Email">
                <small class="error"></small>
            </div>

            <button type="submit" name="send" class="auth-btn">Send Link</button>

        </form>
    </div>
</div>

<script src="assets/js/auth-validation.js"></script>
<script src="assets/js/success-errorBox.js"></script>
<script src="assets/js/toggle-password.js"></script>

<?php include 'includes/footer.php'; ?>