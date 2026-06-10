<?php

include 'includes/header.php';
include 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: signin");
    exit;
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT * FROM users WHERE id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();


if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if (isset($_POST['update_profile'])) {

    if (
        !isset($_POST['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
    ) {
        header("Location: profile?error=failed");
        exit;
    }

    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);

    if (empty($name) || empty($email) || empty($phone) || empty($address)) {
        header("Location: profile?error=required");
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: profile?error=invalid_email");
        exit;
    }

    $stmt = $conn->prepare("
        UPDATE users 
        SET name=?, email=?, phone=?, address=?, last_update=NOW()
        WHERE id=?
    ");

    $stmt->bind_param("ssssi", $name, $email, $phone, $address, $user_id);

    if ($stmt->execute()) {

        $_SESSION['user_name'] = $name;

        header("Location: profile?success=updated");
        exit;
    } else {
        header("Location: profile?error=failed");
        exit;
    }
}

if (isset($_POST['change_password'])) {

    if (
        !isset($_POST['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
    ) {
        header("Location: profile?error=failed");
        exit;
    }

    $current = $_POST['current_password'];
    $new = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];

    $stmt = $conn->prepare("SELECT password FROM users WHERE id=?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $dbUser = $stmt->get_result()->fetch_assoc();

    if (!password_verify($current, $dbUser['password'])) {
        header("Location: profile?error=wrong_password");
        exit;
    }

    if ($new !== $confirm) {
        header("Location: profile?error=not_match");
        exit;
    }

    $hashed = password_hash($new, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("UPDATE users SET password=?, last_update=NOW() WHERE id=?");
    $stmt->bind_param("si", $hashed, $user_id);
    $stmt->execute();

    header("Location: profile?success=password_changed");
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
        <p>View and update your personal information</p>

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

            <div class="form-group password-group">
                <input type="password" name="current_password" placeholder="Current Password">
                <button type="button" class="toggle-password">
                    <i class="fa-solid fa-eye"></i>
                </button>
                <small class="error"></small>
            </div>

            <div class="form-group password-group">
                <input type="password" name="new_password" id="password" placeholder="New Password">
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

            <button type="submit" name="change_password" class="auth-btn">
                Change Password
            </button>

        </form>

    </div>
</div>

<script src="assets/js/auth-validation.js"></script>
<script src="assets/js/success-errorBox.js"></script>
<script src="assets/js/toggle-password.js"></script>

<?php include 'includes/footer.php'; ?>