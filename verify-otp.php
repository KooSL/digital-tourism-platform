<?php
$pageTitle = "Verify OTP";
include 'includes/header.php'; ?>

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

    // Cap OTP guesses at 5 per 5-minute window (matches the OTP's own
    // expiry) - without this, a 6-digit OTP can be brute-forced by simply
    // POSTing every combination before it expires.
    if (!checkRateLimit('otp_verify', 5, 300)) {
        header("Location: signup?error=too_many_attempts");
        exit;
    }

    $entered_otp = trim($_POST['otp'] ?? '');

    if (time() > $_SESSION['otp_expire']) {
        header("Location: signup?error=otp_expired");
        exit;
    }

    // hash_equals gives a constant-time comparison, closing the (small but
    // free-to-fix) timing side-channel that a plain == comparison has.
    if (hash_equals((string)$_SESSION['otp'], (string)$entered_otp)) {

        resetRateLimit('otp_verify');

        $data = $_SESSION['signup_data'];

        $stmt = $conn->prepare("
            INSERT INTO users (name, email, phone, address, password, country)
            VALUES (?, ?, ?, ?, ?, ?)
        ");

        $stmt->bind_param(
            "ssssss",
            $data['name'],
            $data['email'],
            $data['phone'],
            $data['address'],
            $data['password'],
            $data['country']
        );

        if ($stmt->execute()) {

            $_SESSION['user_id'] = $stmt->insert_id;
            $_SESSION['user_name'] = $data['name'];

            // header("Location: index?success=signup");
            // exit;

            if (!empty($data['redirect'])) {

                $redirect = safeInternalRedirect($data['redirect']);

                unset($data['redirect']);

                header("Location: " . $redirect . (strpos($redirect, '?') === false ? '?' : '&') . "success=signup");
            } else {

                header("Location: index?success=signup");
            }

            unset($_SESSION['otp']);
            unset($_SESSION['signup_data']);
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