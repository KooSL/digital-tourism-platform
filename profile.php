<div class="header-wrapper">
    <?php include 'includes/topbar.php'; ?>
    <?php include 'includes/navbar.php'; ?>
</div>

<section class="page-banner">

    <?php if (isset($_GET['success'])): ?>
        <div class="success-box-contact" id="successBox">
            <strong>Success!</strong>
            <?php
            if ($_GET['success'] === 'updated') echo "Profile updated successfully.";
            if ($_GET['success'] === 'password_changed') echo "Password changed successfully.";
            ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="error-box" id="errorBox">
            <strong>Error!</strong>
            <?php
            if ($_GET['error'] === 'invalid_email') echo "Invalid email format!";
            if ($_GET['error'] === 'failed') echo "Failed to update profile!";
            if ($_GET['error'] === 'required') echo "All fields are required!";
            if ($_GET['error'] === 'wrong_password') echo "Current password is incorrect!";
            if ($_GET['error'] === 'not_match') echo "New passwords do not match!";
            ?>
        </div>
    <?php endif; ?>

    <div class="overlay">
        <h1>My Profile</h1>
        <p>Update your personal information</p>

    </div>
</section>

<div class="auth-container">
    <div class="auth-form">

        <p class="last-signin">
            Last Signin:
            <?php echo $user['last_signin'] ? $user['last_signin'] : 'First Signin'; ?>
        </p>


        <form method="POST" id="profileForm" novalidate autocomplete="off">

            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

            <div class="form-group">
                <input type="text" name="name" id="name"
                    value="<?php echo htmlspecialchars($user['name']); ?>">
                <small class="error"></small>
            </div>

            <div class="form-group">
                <input type="email" name="email" id="email"
                    value="<?php echo htmlspecialchars($user['email']); ?>">
                <small class="error"></small>
            </div>

            <div class="form-group">
                <input type="text" name="phone" id="phone"
                    value="<?php echo htmlspecialchars($user['phone']); ?>">
                <small class="error"></small>
            </div>

            <div class="form-group">
                <input type="text" name="address" id="address"
                    value="<?php echo htmlspecialchars($user['address']); ?>">
                <small class="error"></small>
            </div>

            <button type="submit" name="update_profile" class="auth-btn">
                Update Profile
            </button>

        </form>

        <form method="POST" class="mt-20" id="passwordForm" novalidate autocomplete="off">

            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

            <div class="form-group">
                <input type="password" name="current_password" placeholder="Current Password">
                <small class="error"></small>
            </div>

            <div class="form-group">
                <input type="password" name="new_password" id="password" placeholder="New Password">
                <small class="error"></small>
            </div>

            <div class="form-group">
                <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm Password">
                <small class="error"></small>
            </div>

            <button type="submit" name="change_password" class="auth-btn">
                Change Password
            </button>

        </form>

    </div>
</div>

<script src="assets/js/auth-validation.js"></script>
<script src="assets/js/success-errorBox.js"></script>

<?php include 'includes/footer.php'; ?>