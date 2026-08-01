<?php
$pageTitle = "Sign Up";
include 'includes/header.php'; ?>

<?php

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

require_once 'config/db.php';
require_once 'includes/mailer.php';
require_once 'api/countries.php';
require_once 'includes/validation.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['signup'])) {

    if (
        !isset($_POST['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
    ) {
        die("CSRF validation failed.");
    }

    // A few signups per session in a short window is plenty for a real
    // person; stops a script from mass-creating accounts.
    if (!checkRateLimit('signup_attempt', 5, 600)) {
        header("Location: signup?error=too_many_attempts");
        exit;
    }

    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $country = trim($_POST['country'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    // Validated with safeInternalRedirect() before ever being used, so a
    // crafted redirect param can't be used for post-signup phishing.
    $redirect = safeInternalRedirect($_POST['redirect'] ?? null, '');

    // ---- Server-side validation, replacing the old die()-on-first-error
    // approach - that gave a blank page with no navbar/footer and threw
    // away everything the user had typed. Now it redirects back to the
    // form with a proper error list and refills every non-password field. ----
    $v = new Validator();
    $v->required('name', $name, 'Full name is required.')
        ->maxLength('name', $name, 100, 'Name is too long.');
    $v->required('email', $email, 'Email is required.')
        ->email('email', $email, 'Please enter a valid email address.');
    $v->required('phone', $phone, 'Phone number is required.')
        ->phone('phone', $phone, 'Please enter a valid phone number.');
    $v->required('address', $address, 'Address is required.')
        ->minLength('address', $address, 5, 'Address must be at least 5 characters.')
        ->maxLength('address', $address, 200, 'Address is too long.');
    $v->required('country', $country, 'Please select your country.')
        ->inArray('country', $country, $countries, 'Please select a valid country from the list.');
    $v->required('password', $password, 'Password is required.')
        ->minLength('password', $password, 8, 'Password must be at least 8 characters.');

    if ($password !== $confirm_password) {
        $v->required('confirm_password', '', 'Passwords do not match.');
    }

    if ($v->fails()) {
        redirectWithErrors('signup', $v->errors(), [
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'address' => $address,
            'country' => $country,
            'redirect' => $redirect,
            // never flash password fields back
        ]);
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
        'password' => $hashedPassword,
        'redirect' => $redirect,
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
            if ($_GET['success'] === 'signup') echo "Sign Up successful! Welcome, " . htmlspecialchars($_SESSION['user_name'] ?? 'User') . ".";
            ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error']) && $_GET['error'] !== 'validation'): ?>
        <div class="error-box" id="errorBox">
            <strong>Error!</strong>
            <?php
            if ($_GET['error'] === 'email_exist') echo "Email already exists.";
            if ($_GET['error'] === 'invalid') echo "Registration failed! Please try again.";
            if ($_GET['error'] === 'otp_expired') echo "OTP has been expired! Please signup again.";
            if ($_GET['error'] === 'too_many_attempts') echo "Too many signup attempts. Please try again in a few minutes.";
            ?>
        </div>
    <?php endif; ?>

    <?php if (($_GET['error'] ?? '') === 'validation') renderValidationErrors(); ?>

    <div class="overlay">
        <h1>Sign Up</h1>
        <p>Join us to access exclusive travel deals and personalized services.</p>
    </div>
</section>

<div class="auth-container">
    <div class="auth-form">

        <form method="POST" id="registerForm" novalidate>

            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <input type="hidden" name="redirect" value="<?= oldInput('redirect', $_GET['redirect'] ?? '') ?>">

            <div class="form-group">
                <input type="text" name="name" id="name" placeholder="Full Name" value="<?= oldInput('name') ?>">
                <small class="error"></small>
            </div>

            <div class="form-group">
                <input type="email" name="email" id="email" placeholder="Email" value="<?= oldInput('email') ?>">
                <small class="error"></small>
            </div>

            <div class="form-group">
                <select name="country" id="country">
                    <?php $oldCountry = oldInput('country'); ?>
                    <option value="" disabled <?= empty($oldCountry) ? 'selected' : '' ?>>Select Country</option>
                    <?php foreach ($countries as $country): ?>
                        <option value="<?= htmlspecialchars($country) ?>" <?= $country === $oldCountry ? 'selected' : '' ?>>
                            <?= htmlspecialchars($country) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <small class="error"></small>
            </div>

            <div class="form-group">
                <input type="text" name="address" id="address" placeholder="Address" value="<?= oldInput('address') ?>">
                <small class="error"></small>
            </div>

            <div class="form-group">
                <input type="text" name="phone" id="phone" placeholder="Phone Number" value="<?= oldInput('phone') ?>">
                <small class="error"></small>
            </div>

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
<script src="assets/js/toggle-password.js"></script>

<?php clearOldInput(); ?>

<?php include 'includes/footer.php'; ?>