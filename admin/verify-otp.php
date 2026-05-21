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