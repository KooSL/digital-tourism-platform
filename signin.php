<?php include 'includes/header.php'; ?>

<?php

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

require_once 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['signin'])) {

    if (
        !isset($_POST['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
    ) {
        die("CSRF validation failed.");
    }

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, name, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {

        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {

            session_regenerate_id(true);

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];

            $stmt = $conn->prepare("UPDATE users SET last_signin=NOW() WHERE id=?");
            $stmt->bind_param("i", $user['id']);
            $stmt->execute();

            header("Location: index?success=signin");
            exit;

            // $redirect = $_GET['redirect'] ?? 'index?success=signin';

            // header("Location: " . $redirect . (strpos($redirect, '?') === false ? '?' : '&') . "success=signin");
            // exit;
        } else {
            header("Location: signin?error=invalid");
            exit;
        }
    } else {
        header("Location: signin?error=not_found");
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
            if ($_GET['success'] === 'signin') echo "Sign in successful!";
            ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="error-box" id="errorBox">
            <strong>Error!</strong>
            <?php
            if ($_GET['error'] === 'invalid') echo "Invalid email or password.";
            if ($_GET['error'] === 'not_found') echo "Account does not exist.";
            ?>
        </div>
    <?php endif; ?>

    <div class="overlay">
        <h1>Sign In</h1>
        <p>Sign in to access your account and personalized services.</p>
    </div>
</section>

<div class="auth-container">
    <div class="auth-form">

        <form method="POST" id="loginForm" novalidate>

            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

            <div class="form-group">
                <input type="email" name="email" id="email" placeholder="Email">
                <small class="error"></small>
            </div>

            <div class="form-group password-group">
                <input type="password" name="password" id="password" placeholder="Password">

                <button type="button" class="toggle-password">
                    <i class="fa-solid fa-eye"></i>
                </button>

                <small class="error"></small>
            </div>

            <button type="submit" name="signin" class="auth-btn">Sign In</button>

            <p class="auth-switch">
                Don’t have an account?
                <a href="signup">Sign Up</a>
            </p>

        </form>

    </div>
</div>

<script src="assets/js/auth-validation.js"></script>
<script src="assets/js/success-errorBox.js"></script>
<script src="assets/js/toggle-password.js"></script>

<?php include 'includes/footer.php'; ?>