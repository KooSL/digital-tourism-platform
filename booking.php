<?php
$pageTitle = "Booking";
include 'includes/header.php'; ?>

<?php

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

include 'config/db.php';
include 'includes/mailer.php';
include 'api/countries.php';
include 'includes/validation.php';

$id = intval($_GET['id']);

if (!isset($_GET['id']) || $id <= 0) {
    header("Location: tours?error=invalid");
    exit;
}

$stmt = mysqli_prepare($conn, "SELECT * FROM tours WHERE id=? AND status=1");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$tour = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$tour) {
    echo "<p class='pageError tournotfound'>Package not found!</p>";
    include 'includes/footer.php';
    exit;
}

$latitude = $tour['latitude'];
$longitude = $tour['longitude'];
$location_name = $tour['location_name'];

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    $user_stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE id=?");
    mysqli_stmt_bind_param($user_stmt, "i", $user_id);
    mysqli_stmt_execute($user_stmt);
    $user_result = mysqli_stmt_get_result($user_stmt);
    $user_data = mysqli_fetch_assoc($user_result);
    mysqli_stmt_close($user_stmt);

    $_SESSION['user_name'] = $user_data['name'];
    $_SESSION['user_email'] = $user_data['email'];
    $_SESSION['user_phone'] = $user_data['phone'];
    $_SESSION['user_country'] = $user_data['country'];
}

if (isset($_POST['book'])) {

    if (
        !isset($_POST['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
    ) {
        die("CSRF validation failed.");
    }

    $package_id = intval($_POST['package_id']);
    $persons = intval($_POST['persons']);
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $country = trim($_POST['country'] ?? '');
    $travel_date = trim($_POST['travel_date'] ?? '');
    $payment_method = trim($_POST['payment_method'] ?? '');

    // Re-fetch the tour SERVER-SIDE to get its real price - never trust a
    // price/amount coming from the client. This also confirms the tour
    // being booked actually exists and is still active.
    $priceStmt = mysqli_prepare($conn, "SELECT price FROM tours WHERE id = ? AND status = 1");
    mysqli_stmt_bind_param($priceStmt, "i", $package_id);
    mysqli_stmt_execute($priceStmt);
    $priceResult = mysqli_stmt_get_result($priceStmt);
    $priceRow = mysqli_fetch_assoc($priceResult);
    mysqli_stmt_close($priceStmt);

    if (!$priceRow) {
        header("Location: booking?id=$package_id&error=required");
        exit;
    }

    // ---- Full server-side validation (previously ONLY persons/price were
    // checked here - name/email/phone/date/country were accepted as-is) ----
    $v = new Validator();
    $v->required('name', $name, 'Full name is required.')
        ->maxLength('name', $name, 100, 'Name is too long.');
    $v->required('email', $email, 'Email is required.')
        ->email('email', $email, 'Please enter a valid email address.');
    $v->required('phone', $phone, 'Phone number is required.')
        ->phone('phone', $phone, 'Please enter a valid phone number.');
    $v->required('country', $country, 'Please select your country.')
        ->inArray('country', $country, $countries, 'Please select a valid country from the list.');
    $v->required('travel_date', $travel_date, 'Travel date is required.')
        ->dateNotPast('travel_date', $travel_date, 'Travel date must be today or a future date.');
    $v->integerRange('persons', $persons, 1, 50, 'Number of persons must be between 1 and 50.');

    if ($v->fails()) {
        redirectWithErrors("booking?id=$package_id", $v->errors(), [
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'country' => $country,
            'travel_date' => $travel_date,
            'persons' => (string)$persons,
        ]);
    }

    $_SESSION['booking_data'] = [
        'package_id' => $package_id,
        'user_id' => $_SESSION['user_id'] ?? null,
        'name' => $name,
        'email' => $email,
        'country' => $country,
        'phone' => $phone,
        'date' => $travel_date,
        'persons' => $persons,
        // 'amount' => (float)$priceRow['price'],
        'amount' => 100.00, // For testing purposes, set a fixed amount of 100.00
    ];

    if ($payment_method === 'esewa') {
        header("Location: payment/esewa-payment?package_id=" . $package_id);
        exit;
    } elseif ($payment_method === 'khalti') {
        // header("Location: payment/khalti-payment?package_id=" . $package_id);
        // exit;
        redirectWithErrors("booking?id=$package_id", ['payment_method' => 'Khalti is currently unavailable. Please choose another payment method.'], [
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'country' => $country,
            'travel_date' => $travel_date,
            'persons' => (string)$persons,
        ]);
    } else {
        redirectWithErrors("booking?id=$package_id", ['payment_method' => 'Please select a payment method.'], [
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'country' => $country,
            'travel_date' => $travel_date,
            'persons' => (string)$persons,
        ]);
    }
}

$avgStmt = mysqli_prepare($conn, "
    SELECT ROUND(AVG(rating),1) AS avg_rating, COUNT(*) AS total_reviews
    FROM trip_reviews
    WHERE trip_id = ? AND status = 1
");
mysqli_stmt_bind_param($avgStmt, "i", $id);
mysqli_stmt_execute($avgStmt);
$ratingData = mysqli_stmt_get_result($avgStmt)->fetch_assoc();
mysqli_stmt_close($avgStmt);

?>

<div class="header-wrapper">
    <?php include 'includes/topbar.php'; ?>
    <?php include 'includes/navbar.php'; ?>
</div>

<!-- BANNER -->
<section class="tour-banner"
    style="background-image: url('uploads/images/tours/<?= htmlspecialchars($tour['banner_image']) ?>');">

    <div class="overlay">
        <div class="container">

            <?php if (isset($_GET['success'])): ?>
                <div class="success-box" id="successBox">
                    <strong>Success!</strong>
                    <?php
                    if ($_GET['success'] === 'sent') echo "Your inquiry has been sent successfully. We’ll contact you soon.";
                    if ($_GET['success'] === 'booked') echo "Your package has been booked successfully. We’ll contact you soon.";
                    if ($_GET['success'] === 'signin') echo "Sign in successful! Welcome, " . htmlspecialchars($_SESSION['user_name'] ?? 'User') . ".";
                    ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['error']) && $_GET['error'] !== 'validation'): ?>
                <div class="error-box package" id="errorBox">
                    <strong>Error!</strong>
                    <?php
                    if ($_GET['error'] === 'failed') echo "Inquiry failed to send. Please try again.";
                    if ($_GET['error'] === 'booking_failed') echo "Booking failed. Please try again.";
                    if ($_GET['error'] === 'required') echo "Please fill in all required fields.";
                    ?>
                </div>
            <?php endif; ?>

            <?php if (($_GET['error'] ?? '') === 'validation') renderValidationErrors(); ?>

            <h1><?= htmlspecialchars($tour['title']) ?></h1>
            <p><?= htmlspecialchars($tour['duration']) ?></p>

            <div class="banner-bottom-info">

                <div id="weatherBox">
                    <p><i class="fa-solid fa-temperature-full"></i>Temperature: Loading weather...</p>
                </div>

                <div class="popular-badge-detail-box">
                    <?php if ($tour['is_popular'] == 1): ?>
                        <span class="popular-badge-detail"><i class="fa-solid fa-fire"></i> Popular</span>
                    <?php endif; ?>
                </div>

                <div class="rating-summary">
                    <a href="tour-details?id=<?= (int)$tour['id'] ?>#reviews"><i class="fa-solid fa-star"></i> <?= $ratingData['avg_rating'] ?? '0.0' ?>
                        (<?= (int)($ratingData['total_reviews'] ?? 0) ?> reviews)</a>
                </div>

            </div>

        </div>
    </div>
</section>

<section class="page-banner">
    <div class="overlay">
        <h1>Book This Package</h1>
        <p>Fill in the details below to book your trip.</p>
    </div>
</section>

<div class="booking-container">
    <div class="booking-form">

        <?php
        if (!isset($_SESSION['user_id'])) { ?>
            <div class="booking-guest-note">
                <strong>You are booking as a guest</strong>
                <p class="note">* Sign in to save your booking history. If you book without signing in, your booking may not appear in the My Bookings.</p>
            </div>
        <?php } ?>

        <form method="POST" novalidate>

            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
            <input type="hidden" name="package_id" value="<?php echo (int)$tour['id']; ?>">

            <div class="form-group">
                <input type="date" name="travel_date" id="travel_date" min="<?= date('Y-m-d') ?>"
                    value="<?= oldInput('travel_date') ?>">
                <small class="error"></small>
            </div>

            <div class="form-group">
                <input type="number" name="persons" placeholder="Number of Persons" min="1" max="50" id="persons"
                    value="<?= oldInput('persons') ?>">
                <small class="error"></small>
            </div>

            <div class="form-group">
                <input type="text" name="name" placeholder="Full Name" id="name"
                    value="<?= oldInput('name', $_SESSION['user_name'] ?? '') ?>">
                <small class="error"></small>
            </div>

            <div class="form-group">
                <input type="email" name="email" placeholder="Email" id="email"
                    value="<?= oldInput('email', $_SESSION['user_email'] ?? '') ?>">
                <small class="error"></small>
            </div>

            <?php $userCountry = oldInput('country', $_SESSION['user_country'] ?? ''); ?>
            <div class="form-group">
                <select name="country" id="country">
                    <option value="" disabled <?= empty($userCountry) ? 'selected' : '' ?>>
                        Select Country
                    </option>

                    <?php foreach ($countries as $country): ?>
                        <option value="<?= htmlspecialchars($country) ?>"
                            <?= $country === $userCountry ? 'selected' : '' ?>>
                            <?= htmlspecialchars($country) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <small class="error"></small>
            </div>

            <div class="form-group">
                <input type="text" name="phone" placeholder="Phone" id="phone"
                    value="<?= oldInput('phone', $_SESSION['user_phone'] ?? '') ?>">
                <small class="error"></small>
            </div>

            <div class="payment-summary">
                <p>Price: NPR <span id="packagePrice"><?= htmlspecialchars($tour['price']) ?></span> / person</p>
                <p class="discount-txt">Discount: <span id="discountText">0%</span></p>
                <hr>
                <p><strong>Total Package Price: NPR <span id="totalAmount"><?= htmlspecialchars($tour['price']) ?></span></strong></p>
            </div>

            <div class="payment-partners">
                <!-- <p>Pay With:</p>
                <div class="payment-icons">
                    <img src="assets/images/payments/esewa_2.png" alt="eSewa">
                </div> -->
                <p>Choose Payment Method</p>
                <div class="payment-methods">
                    <label>
                        <input type="radio" name="payment_method" id="payment_method" value="esewa">
                        <div class="payment-icons">
                            <img src="assets/images/payments/esewa_2.png" alt="eSewa">
                        </div>
                    </label>
                    <label>
                        <input type="radio" name="payment_method" id="payment_method" value="khalti">
                        <div class="payment-icons">
                            <img src="assets/images/payments/khalti_2.png" alt="Khalti">
                        </div>
                    </label>
                </div>
            </div>

            <button type="submit" class="booking-btn" name="book">Proceed to Payment</button>

        </form>
    </div>
</div>

<script src="assets/js/auth-validation.js"></script>
<script src="assets/js/success-errorBox.js"></script>

<script>
    const pricePerPerson = <?= json_encode((float)$tour['price']) ?>;
</script>

<script>
    const latitude = <?= json_encode((float)$latitude) ?>;
    const longitude = <?= json_encode((float)$longitude) ?>;
    const locationName = <?= json_encode($location_name) ?>;
</script>

<script src="api/weather.js"></script>

<script src="assets/js/tripCost-calc.js"></script>

<?php clearOldInput(); ?>

<?php include 'includes/footer.php'; ?>