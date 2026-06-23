<?php include 'includes/header.php'; ?>

<?php

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

require_once 'config/db.php';
require_once 'includes/mailer.php';


$token = $_GET['token'];

$stmt = $conn->prepare(
    "SELECT user_id 
FROM password_resets
WHERE token=?
AND expires_at > NOW()"
);

$stmt->bind_param("s", $token);

$stmt->execute();

$result = $stmt->get_result()->fetch_assoc();

$user_id = $result['user_id'];

if (!$result) {
    $delete = $conn->prepare(
        "DELETE FROM password_resets WHERE token=?"
    );
    $delete->bind_param("s", $token);
    $delete->execute();

    header("Location: forgot-password?error=invalid_expired");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['send'])) {

    if (
        !isset($_POST['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
    ) {
        die("CSRF validation failed.");
    }

    $newPassword =
        password_hash(
            $_POST['password'],
            PASSWORD_DEFAULT
        );

    $stmt = $conn->prepare(
        "UPDATE users
        SET password=?
        WHERE id=?"
    );

    $stmt->bind_param(
        "si",
        $newPassword,
        $user_id
    );

    $stmt->execute();

    $stmt = $conn->prepare(
        "DELETE FROM password_resets
        WHERE token=?"
    );

    $stmt->bind_param(
        "s",
        $token
    );

    $stmt->execute();

    header("Location: signin?success=pw_reset");
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
            if ($_GET['error'] === 'email_doesnt_exist') echo "Account does not exist.";
            if ($_GET['error'] === 'invalid') echo "Registration failed! Please try again.";
            if ($_GET['error'] === 'otp_expired') echo "OTP has been expired! Please signup again.";
            ?>
        </div>
    <?php endif; ?>

    <div class="overlay">
        <h1>Reset Password</h1>
        <p>Enter your new password.</p>
    </div>
</section>

<div class="auth-container">
    <div class="auth-form">

        <form method="POST" id="registerForm" novalidate>

            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

            <div class="form-group password-group">
                <input type="password" name="password" id="password" placeholder="Password">

                <button type="button" class="toggle-password">
                    <i class="fa-solid fa-eye"></i>
                </button>

                <small class="error"></small>
            </div>

            <div class="form-group password-group">
                <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm Password">

                <button type="button" class="toggle-password">
                    <i class="fa-solid fa-eye"></i>
                </button>

                <small class="error"></small>
            </div>

            <button type="submit" name="send" class="auth-btn">Update</button>

        </form>
    </div>
</div>

<script src="assets/js/auth-validation.js"></script>
<script src="assets/js/success-errorBox.js"></script>
<script src="assets/js/toggle-password.js"></script>

<?php include 'includes/footer.php'; ?>