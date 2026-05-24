<?php include 'includes/header.php'; ?>

<?php

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

require_once 'config/db.php';

if (!isset($_SESSION['signup_data'])) {
    header("Location: signup");
    exit;
}

if (isset($_POST['verify'])) {

    if (
        !isset($_POST['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
    ) {
        die("CSRF validation failed.");
    }

    $entered_otp = $_POST['otp'];

    if (time() > $_SESSION['otp_expire']) {
        header("Location: signup?error=otp_expired");
        exit;
    }

    if ($entered_otp == $_SESSION['otp']) {

        $data = $_SESSION['signup_data'];

        $stmt = $conn->prepare("
            INSERT INTO users (name, email, phone, address, password)
            VALUES (?, ?, ?, ?, ?)
        ");

        $stmt->bind_param(
            "sssss",
            $data['name'],
            $data['email'],
            $data['phone'],
            $data['address'],
            $data['password']
        );

        if ($stmt->execute()) {

            $_SESSION['user_id'] = $stmt->insert_id;
            $_SESSION['user_name'] = $data['name'];

            // clear temp data
            unset($_SESSION['otp']);
            unset($_SESSION['signup_data']);

            header("Location: index?success=signup");
            exit;
        }
    } else {
        header("Location: verify-otp?error=invalid_otp");
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
            <?php
            if ($_GET['success'] === 'signup') echo "Sign Up successful! Welcome, " . (isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'User') . ".";
            ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="error-box" id="errorBox">
            <strong>Error!</strong>
            <?php
            if ($_GET['error'] === 'invalid_otp') echo "Invalid OTP! Please try again.";
            ?>
        </div>
    <?php endif; ?>

    <div class="overlay">
        <h1>Verify OTP</h1>
        <p>Enter the code sent to your email</p>

    </div>
</section>

<div class="auth-container">
    <div class="auth-form">


        <form method="POST" novalidate>

            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

            <div class="form-group">
                <input type="number" name="otp" id="otp" placeholder="Enter OTP">
                <small class="error"></small>
            </div>

            <button type="submit" name="verify" class="auth-btn">
                Verify
            </button>

        </form>

    </div>
</div>

<script src="assets/js/auth-validation.js"></script>
<script src="assets/js/success-errorBox.js"></script>

<?php include 'includes/footer.php'; ?>