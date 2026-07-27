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

    // $user_name = $user_data['name'];
    // $user_email = $user_data['email'];
    // $user_phone = $user_data['phone'];
    $_SESSION['user_name'] = $user_data['name'];
    $_SESSION['user_email'] = $user_data['email'];
    $_SESSION['user_phone'] = $user_data['phone'];
    $_SESSION['user_country'] = $user_data['country'];
}


// if (isset($_POST['book'])) {

//     $package_id = intval($_POST['package_id']);
//     $user_id = $_SESSION['user_id'] ?? null;

//     $name = $_POST['name'];
//     $email = $_POST['email'];
//     $phone = $_POST['phone'];
//     $date = $_POST['travel_date'];
//     $persons = intval($_POST['persons']);

//     if (!$name || !$phone || !$date || $persons < 1) {
//         header("Location: booking?id=$package_id&error=required");
//         exit;
//     }

//     $stmt = $conn->prepare("
//         INSERT INTO package_bookings
//         (package_id, user_id, name, email, phone, travel_date, persons)
//         VALUES (?, ?, ?, ?, ?, ?, ?)
//     ");

//     $stmt->bind_param(
//         "iissssi",
//         $package_id,
//         $user_id,
//         $name,
//         $email,
//         $phone,
//         $date,
//         $persons
//     );

//     if ($stmt->execute()) {

//         $subject = "New Package Booking";

//         $body = "
//           <h3>New Booking</h3>
//           <p>Name: $name</p>
//           <p>Phone: $phone</p>
//           <p>Date: $date</p>
//           <p>Persons: $persons</p>
//         ";

//         sendAdminMail($subject, $body);

//         header("Location: tour-details?id=$package_id&success=booked");
//         exit;
//         header("Location: esewa-payment");
//         exit;
//     } else {
//         header("Location: tour-details?id=$package_id&error=booking_failed");
//         exit;
//     }
// }

if (isset($_POST['book'])) {

    if (
        !isset($_POST['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
    ) {
        die("CSRF validation failed.");
    }

    $package_id = intval($_POST['package_id']);
    $persons = intval($_POST['persons']);

    // Re-fetch the tour SERVER-SIDE to get its real price - never trust a
    // price/amount coming from the client. The old code hardcoded
    // 'amount' => 5 (leftover test value), which meant every booking,
    // regardless of the actual tour or number of people, was charged the
    // same fixed 5 (currency unit) amount. This also closes a price-
    // tampering vector: an attacker can't submit their own 'amount' field
    // because we never read one from $_POST at all.
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

    if ($persons < 1 || $persons > 50) {
        header("Location: booking?id=$package_id&error=required");
        exit;
    }

    $_SESSION['booking_data'] = [
        'package_id' => $package_id,
        'user_id' => $_SESSION['user_id'] ?? null,
        'name' => trim($_POST['name'] ?? ''),
        'email' => trim($_POST['email'] ?? ''),
        'country' => trim($_POST['country'] ?? ''),
        'phone' => trim($_POST['phone'] ?? ''),
        'date' => $_POST['travel_date'] ?? '',
        'persons' => $persons,
        'amount' => (float)$priceRow['price'], // per-person price, server-verified
    ];

    header("Location: esewa-payment?package_id=" . $package_id);
    exit;
}

$avg = mysqli_query(
    $conn,
    "SELECT
        ROUND(AVG(rating),1) AS avg_rating,
        COUNT(*) AS total_reviews
     FROM trip_reviews
     WHERE trip_id = $id
     AND status = 1"
);

$ratingData = mysqli_fetch_assoc($avg);

?>

<div class="header-wrapper">
    <?php include 'includes/topbar.php'; ?>
    <?php include 'includes/navbar.php'; ?>
</div>

<?php
if ($id <= 0) {
    echo "<p class='pageError invalidId'>Invalid Package ID!</p>";
    exit;
}

if (!$tour) {
    echo "<p class='pageError tournotfound'>Package not found!</p>";
    exit;
}
?>

<!-- BANNER -->
<section class="tour-banner"
    style="background-image: url('uploads/images/tours/<?= $tour['banner_image'] ?>');">

    <div class="overlay">
        <div class="container">

            <?php if (isset($_GET['success'])): ?>
                <div class="success-box" id="successBox">
                    <strong>Success!</strong>
                    <?php
                    if ($_GET['success'] === 'sent') echo "Your inquiry has been sent successfully. We’ll contact you soon.";
                    if ($_GET['success'] === 'booked') echo "Your package has been booked successfully. We’ll contact you soon.";
                    if ($_GET['success'] === 'signin') echo "Sign in successful! Welcome, " . (isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'User') . ".";
                    ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['error'])): ?>
                <div class="error-box package" id="errorBox">
                    <strong>Error!</strong>
                    <?php
                    if ($_GET['error'] === 'failed') echo "Inquiry failed to send. Please try again.";
                    if ($_GET['error'] === 'booking_failed') echo "Booking failed. Please try again.";
                    if ($_GET['error'] === 'required') echo "Please fill in all required fields.";
                    ?>
                </div>
            <?php endif; ?>

            <h1><?= $tour['title'] ?></h1>
            <p><?= $tour['duration'] ?></p>

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
                    <a href="tour-details?id=<?= $tour['id'] ?>#reviews"><i class="fa-solid fa-star"></i> <?= $ratingData['avg_rating'] ?? '0.0' ?>
                        (<?= $ratingData['total_reviews'] ?> reviews)</a>
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
                <input type="date" name="travel_date" id="travel_date" min="<?= date('Y-m-d') ?>">
                <small class="error"></small>
            </div>

            <div class="form-group">
                <input type="number" name="persons" placeholder="Number of Persons" min="1" id="persons">
                <small class="error"></small>
            </div>

            <div class="form-group">
                <input type="text" name="name" placeholder="Full Name" id="name"
                    value="<?php echo $_SESSION['user_name'] ?? ''; ?>">
                <small class="error"></small>
            </div>

            <div class="form-group">
                <input type="email" name="email" placeholder="Email" id="email"
                    value="<?php echo $_SESSION['user_email'] ?? ''; ?>">
                <small class="error"></small>
            </div>

            <?php $userCountry = $_SESSION['user_country'] ?? ''; ?>
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
                    value="<?php echo $_SESSION['user_phone'] ?? ''; ?>">
                <small class="error"></small>
            </div>

            <div class="payment-summary">
                <p>Price: NPR <span id="packagePrice"><?php echo $tour['price']; ?></span> / person</p>
                <p class="discount-txt">Discount: <span id="discountText">0%</span></p>
                <hr>
                <p><strong>Total Payable: NPR <span id="totalAmount"><?php echo $tour['price']; ?></span></strong></p>
            </div>

            <div class="payment-partners">
                <p>Pay With:</p>
                <div class="payment-icons">
                    <img src="assets/images/payments/esewa_2.png" alt="eSewa">
                </div>
            </div>

            <button type="submit" class="booking-btn" name="book">Proceed to Payment</button>

        </form>
    </div>
</div>

<script src="assets/js/auth-validation.js"></script>
<script src="assets/js/success-errorBox.js"></script>

<script>
    const pricePerPerson = <?= $tour['price']; ?>;
</script>

<script>
    const latitude = <?= $latitude ?>;
    const longitude = <?= $longitude ?>;
    const locationName = "<?= addslashes($location_name) ?>";
</script>

<script src="api/weather.js"></script>

<script src="assets/js/tripCost-calc.js"></script>

<?php include 'includes/footer.php'; ?>