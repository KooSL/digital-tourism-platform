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
            $_SESSION['success'] = "Sign in successful! Welcome, $user[name].";

            header("Location: index?success=1");
            exit;
        } else {
            $_SESSION['error'] = "Invalid email or password.";
            header("Location: signin?error=1");
            exit;
        }
    } else {
        $_SESSION['error'] = "Account does not exist.";
        header("Location: signin?error=1");
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
            <p><?php echo isset($_SESSION['success']) ? $_SESSION['success'] : 'Sign in successful!'; ?></p>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="error-box" id="errorBox">
            <strong>Error!</strong>
            <p><?php echo isset($_SESSION['error']) ? $_SESSION['error'] : 'Something went wrong!'; ?></p>
        </div>
    <?php endif; ?>

    <div class="overlay">
        <h1>Sign In</h1>
        <p>Sign in to access your account and personalized services.</p>
    </div>
</section>

<div class="contact-form-box">
    <div class="container">

        <form method="POST" id="registerForm" novalidate>

            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

            <div class="form-group">
                <input type="email" name="email" id="email" placeholder="Email">
                <small class="error"></small>
            </div>

            <div class="form-group">
                <input type="password" name="password" id="password" placeholder="Password">
                <small class="error"></small>
            </div>

            <p class="acc-sign-txt">Create an account <a href="signup">Sign Up</a></p>

            <button type="submit" name="signin">Sign In</button>
        </form>
    </div>
</div>

<script src="assets/js/auth-validation.js"></script>
<script src="assets/js/success-errorBox.js"></script>

<?php include 'includes/footer.php'; ?>
