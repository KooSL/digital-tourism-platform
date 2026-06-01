<?php include 'includes/header.php'; ?>

<?php

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

include 'config/db.php';
include 'includes/mailer.php';
include 'api/countries.php';

$id = intval($_GET['id'] ?? 0);

$stmt = mysqli_prepare($conn, "SELECT * FROM tours WHERE id=? AND status=1");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$tour = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

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

    $_SESSION['booking_data'] = [
        'package_id' => intval($_POST['package_id']),
        'user_id' => $_SESSION['user_id'] ?? null,
        'name' => $_POST['name'],
        'email' => $_POST['email'],
        'country' => $_POST['country'],
        'phone' => $_POST['phone'],
        'date' => $_POST['travel_date'],
        'persons' => intval($_POST['persons']),
        'amount' => 5
    ];

    header("Location: esewa-payment?package_id=" . $_POST['package_id']);
    exit;
}
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
    style="background-image: url('admin/uploads/images/tours/<?= $tour['banner_image'] ?>');">

    <div class="overlay">
        <div class="container">

            <?php if (isset($_GET['success']) && $_GET['success'] === 'sent'): ?>
                <div class="success-box" id="successBox">
                    <strong>Success!</strong>
                    <?php
                    if ($_GET['success'] === 'sent') echo "Your inquiry has been sent successfully. We’ll contact you soon.";
                    ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['error'])): ?>
                <div class="error-box package" id="errorBox">
                    <strong>Error!</strong>
                    <?php
                    if ($_GET['error'] === 'required') echo "Please fill in all required fields.";

                    ?>
                </div>
            <?php endif; ?>

            <h1><?= $tour['title'] ?></h1>
            <p><?= $tour['duration'] ?></p>

            <?php if ($tour['is_popular'] == 1): ?>
                <span class="popular-badge-detail"><i class="fa-solid fa-fire"></i> Popular</span>
            <?php endif; ?>

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

            <input type="hidden" name="package_id" value="<?php echo $tour['id']; ?>">

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
                <p>Discount: <span id="discountText">0%</span></p>
                <hr>
                <p><strong>Total Payable: NPR <span id="totalAmount"><?php echo $tour['price']; ?></span></strong></p>
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

<script src="assets/js/tripCost-calc.js"></script>

<?php include 'includes/footer.php'; ?>