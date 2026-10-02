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
include 'includes/pricing.php';

// $id = intval($_GET['id']);
$slug = $_GET['trip'];

if (!isset($_GET['trip']) || empty($slug)) {
    header("Location: trips?error=invalid");
    exit;
}

$stmt = mysqli_prepare($conn, "SELECT * FROM trips WHERE slug=? AND status=1");
mysqli_stmt_bind_param($stmt, "s", $slug);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$trip = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);
$id = $trip['id'] ?? null;

if (!$trip) {
    header("Location: trips?error=not_found");
    exit;
}

$latitude = $trip['latitude'];
$longitude = $trip['longitude'];
$location_name = $trip['location_name'];

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
        header("Location: booking?trip=$slug&type=" . urlencode($trip['type']) . "&price=" . urlencode($trip['price']) . "&id=" . urlencode($trip['id']) . "&error=invalid");
        exit;
    }

    $package_id = intval($_POST['package_id']);
    $persons = intval($_POST['persons']);
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $country = trim($_POST['country'] ?? '');
    $travel_date = trim($_POST['travel_date'] ?? '');
    $payment_method = trim($_POST['payment_method'] ?? '');
    $payment_option = trim($_POST['payment_option'] ?? '');

    // Re-fetch the trip SERVER-SIDE to get its real price - never trust a
    // price/amount coming from the client. This also confirms the trip
    // being booked actually exists and is still active.
    $priceStmt = mysqli_prepare($conn, "SELECT price FROM trips WHERE id = ? AND slug = ? AND status = 1");
    mysqli_stmt_bind_param($priceStmt, "is", $package_id, $slug);
    mysqli_stmt_execute($priceStmt);
    $priceResult = mysqli_stmt_get_result($priceStmt);
    $priceRow = mysqli_fetch_assoc($priceResult);
    mysqli_stmt_close($priceStmt);

    if (!$priceRow) {
        header("Location: booking?trip=$slug&type=" . urlencode($trip['type']) . "&price=" . urlencode($trip['price']) . "&id=" . urlencode($trip['id']) . "&error=required");
        exit;
    }

    // ---- Full server-side validation ----
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
    // "Pay full" vs "pay 10% deposit now" - never trust a client-sent
    // amount, only this choice; the actual amounts are computed
    // server-side from the real trip price in esewa-payment.php.
    $v->required('payment_option', $payment_option, 'Please choose to pay in full or pay a deposit.')
        ->inArray('payment_option', $payment_option, ['full', 'deposit'], 'Please choose a valid payment option.');

    if ($v->fails()) {
        redirectWithErrors("booking?trip=$slug&type=" . urlencode($trip['type']) . "&price=" . urlencode($trip['price']) . "&id=" . urlencode($trip['id']), $v->errors(), [
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
        'package_slug' => $slug,
        'pckg_type' => $trip['type'],
        'user_id' => $_SESSION['user_id'] ?? null,
        'name' => $name,
        'email' => $email,
        'country' => $country,
        'phone' => $phone,
        'pckg_price' => $trip['price'],
        'date' => $travel_date,
        'persons' => $persons,
        'amount' => (float)$priceRow['price'], // per-person price, server-verified
        'payment_option' => $payment_option,   // 'full' or 'deposit' - amounts computed at payment-init time
    ];

    if ($payment_method === 'esewa') {
        header("Location: payment/esewa-payment?slug=" . urlencode($slug));
        exit;
    } elseif ($payment_method === 'khalti') {
        header("Location: payment/khalti-payment?slug=" . urlencode($slug));
        exit;
    } else {
        redirectWithErrors("booking?trip=$slug", ['payment_method' => 'Please select a payment method.'], [
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
<section class="trip-banner"
    style="background-image: url('uploads/images/trips/<?= htmlspecialchars($trip['banner_image']) ?>');">

    <div class="overlay">
        <div class="container">

            <?php if (isset($_GET['success'])): ?>
                <div class="success-box-imgbanner" id="successBox">
                    <?php
                    // if ($_GET['success'] === 'booked') echo "Your package has been booked successfully. We’ll contact you soon.";
                    ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['error']) && $_GET['error'] !== 'validation'): ?>
                <div class="error-box-imgbanner package" id="errorBox">
                    <?php
                    if ($_GET['error'] === 'booking_failed') echo "Booking failed or cancelled. Please try again.";
                    if ($_GET['error'] === 'required') echo "Please fill in all required fields.";
                    if ($_GET['error'] === 'invalid') echo "Invalid request. Please try again.";
                    if ($_GET['error'] === 'too_many_attempts') echo "Too many submissions recently. Please try again in a few minutes.";
                    if ($_GET['error'] === 'verification_failed') echo "We couldn't verify your payment. If money was deducted, it will be refunded automatically, or please contact support with your transaction details.";
                    if ($_GET['error'] === 'initiate_failed') echo "We couldn't start the Khalti payment process. Please try again.";
                    if ($_GET['error'] === 'mobile_banking') echo "Mobile banking payments are not yet supported. Please use eSewa or Khalti.";
                    ?>
                </div>
            <?php endif; ?>

            <?php if (($_GET['error'] ?? '') === 'validation') renderValidationErrors(); ?>

            <h1><?= htmlspecialchars($trip['title']) ?></h1>
            <p><?= htmlspecialchars($trip['duration']) ?></p>

            <div class="banner-bottom-info">

                <div id="weatherBox">
                    <p><i class="fa-solid fa-temperature-full"></i>Temperature: Loading weather...</p>
                </div>

                <div class="popular-badge-detail-box">
                    <?php if ($trip['is_popular'] == 1): ?>
                        <span class="popular-badge-detail"><i class="fa-solid fa-fire"></i> Popular</span>
                    <?php endif; ?>
                </div>

                <div class="rating-summary">
                    <a href="trip-details?trip=<?= urlencode($trip['slug']) ?>&type=<?= urlencode($trip['type']) ?>#reviews"><i class="fa-solid fa-star"></i> <?= $ratingData['avg_rating'] ?? '0.0' ?>
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
        <div class="booking-form-sec-left">

            <?php
            if (!isset($_SESSION['user_id'])) { ?>
                <div class="booking-guest-note">
                    <strong>You are booking as a guest</strong>
                    <p class="note">* Sign in to save your booking history. If you book without signing in, your booking may not appear in the My Bookings.</p>
                </div>
            <?php } ?>

            <form method="POST" novalidate>

                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                <input type="hidden" name="package_id" value="<?php echo (int)$trip['id']; ?>">

                <div class="form-group">
                    <input type="date" name="travel_date" id="travel_date" min="<?= date('Y-m-d') ?>"
                        value="<?= oldInput('travel_date') ?>">
                    <small class="error"></small>
                </div>

                <div class="form-group">
                    <input type="number" name="persons" placeholder="Number of Persons" min="1" max="50" id="persons"
                        value="<?= oldInput('persons') ?: 1 ?>">
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

        </div>

        <div class="booking-form-sec-right">
            <div class="payment-summary">
                <p>Price: NPR <span id="packagePrice"><?= htmlspecialchars($trip['price']) ?></span> / person</p>
                <p class="discount-txt">Discount: <span id="discountText">0%</span></p>
                <hr>
                <p><strong>Total Package Price: NPR <span id="totalAmount"><?= htmlspecialchars($trip['price']) ?></span></strong></p>

                <!-- PAY FULL vs PAY DEPOSIT -->
                <div class="payment-option-group">
                    <p class="payment-option-title">How much would you like to pay now?</p>

                    <label class="payment-option-choice">
                        <input type="radio" name="payment_option" value="full" id="payFull" checked>
                        <span>Pay Full Amount - NPR <span id="fullAmountText"><?= htmlspecialchars($trip['price']) ?></span></span>
                    </label>

                    <label class="payment-option-choice">
                        <input type="radio" name="payment_option" value="deposit" id="payDeposit">
                        <span>
                            Pay 10% Deposit Now - NPR <span id="depositAmountText">0</span>
                            <br>
                            <small>Remaining balance NPR <span id="remainingAmountText">0</span> due later - contact us to arrange final payment before your trip.</small>
                        </span>
                    </label>
                </div>
                <small class="error"></small>
            </div>

            <div class="payment-partners">
                <p>Choose Payment Method</p>
                <div class="payment-methods">
                    <label class="payment-method-choice">
                        <input type="radio" name="payment_method" id="payment_method" value="esewa" checked>
                        <div class="payment-icons">
                            <img src="assets/images/payments/esewa_2.png" alt="eSewa">
                        </div>
                    </label>
                    <label class="payment-method-choice">
                        <input type="radio" name="payment_method" id="payment_method" value="khalti">
                        <div class="payment-icons">
                            <img src="assets/images/payments/khalti_2.png" alt="Khalti">
                        </div>
                    </label>
                    <label class="payment-method-choice">
                        <!-- <input type="radio" name="payment_method" id="payment_method" value="khalti"> -->
                        <div class="payment-icons mobile-banking">
                            <a href="booking?trip=<?= urlencode($trip['slug']) ?>&type=<?= urlencode($trip['type']) ?>&price=<?= urlencode($trip['price']) ?>&id=<?= urlencode($trip['id']) ?>&error=mobile_banking">
                                <img src="assets/images/payments/mobile-banking.jpg" alt="Khalti">
                            </a>
                        </div>
                    </label>
                </div>
            </div>

            <button type="submit" class="booking-btn" name="book">Proceed to Payment</button>
        </div>
        </form>
    </div>
</div>

<script src="assets/js/auth-validation.js"></script>
<script src="assets/js/success-errorBox.js"></script>

<script>
    const pricePerPerson = <?= json_encode((float)$trip['price']) ?>;
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