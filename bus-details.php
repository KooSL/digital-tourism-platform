<?php

session_start();

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

include 'config/db.php';
include 'includes/mailer.php';

$id = intval($_GET['id'] ?? 0);

if (isset($_POST['send_inquiry'])) {

    if (
        !isset($_POST['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
    ) {
        die("CSRF validation failed.");
    }

    $bus_id = intval($_POST['bus_id']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    // $date = $_POST['travel_date'];
    $message = mysqli_real_escape_string($conn, $_POST['message']);

    $stmt = $conn->prepare("
    INSERT INTO bus_inquiries (bus_id, name, email, phone, message)
    VALUES (?, ?, ?, ?, ?)
    ");

    $stmt->bind_param("issss", $bus_id, $name, $email, $phone, $message);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {

        $subject = "New Bus Inquiry";

        $body = "
      <h3>New Bus Inquiry Received</h3>
      <p><strong>Name:</strong> $name</p>
      <p><strong>Email:</strong> $email</p>
      <p><strong>Phone:</strong> $phone</p>
      <p><strong>Message:</strong> $message</p>
    ";

        sendAdminMail($subject, $body);

        header("Location: bus-details?id=$id&success=1");
        exit;
    } else {
        die("Failed to submit inquiry. Please try again.");
    }
}
?>

<?php include 'includes/header.php'; ?>

<div class="header-wrapper">
    <?php include 'includes/topbar.php'; ?>
    <?php include 'includes/navbar.php'; ?>
</div>

<?php

if (!isset($_GET['id'])) {
    header("Location: buses");
    exit;
}


$query = mysqli_query($conn, "SELECT * FROM buses WHERE id = $id LIMIT 1");
$bus = mysqli_fetch_assoc($query);

if (!$bus) {
    echo "<p>Bus not found.</p>";
    exit;
}
?>

<!-- PAGE BANNER -->
<section class="page-banner">



    <div class="overlay">

        <div id="notify"></div>

        <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
            <div class="success-box" id="successBox">
                <strong>Success!</strong>
                <p>Your inquiry has been sent successfully. We’ll contact you soon.</p>
            </div>
        <?php endif; ?>

        <h1>Bus Details</h1>

        <div class="busdestination">
            <strong>From:</strong> <?php echo htmlspecialchars($bus['from_location']); ?>
            <strong>To:</strong> <?php echo htmlspecialchars($bus['to_location']); ?>
        </div>
    </div>
</section>

<!-- BUS DETAILS -->
<section class="bus-details-section">

    <div class="container bus-details-grid">

        <!-- LEFT IMAGE -->
        <div class="bus-image">
            <img src="admin/uploads/images/buses/<?php echo htmlspecialchars($bus['banner_image']); ?>" alt="">
        </div>

        <!-- RIGHT CONTENT -->
        <div class="bus-details">

            <!-- BADGES -->
            <!-- <div class="badge-container">

                <?php if ($bus['bus_type']): ?>
                    <div class="group-fare-badge-details">
                        <i class="fa-solid fa-bus"></i> <?php echo htmlspecialchars($bus['bus_type']); ?>
                    </div>
                <?php endif; ?>

            </div> -->

            <!-- DETAILS -->

            <div class="bus-details-box">
                <p><strong>Bus Name:</strong> <?php echo $bus['bus_name']; ?></p>
                <p><strong>Bus Number:</strong> <?php echo $bus['bus_number']; ?></p>
                <p><strong>Departure Time:</strong> <?php echo $bus['departure_time']; ?></p>
                <p><strong>Arrival Time:</strong> <?php echo $bus['arrival_time']; ?></p>
                <p><strong>Total Seats:</strong> <?php echo $bus['total_seats']; ?></p>
                <p><strong>Price:</strong> NPR <?php echo $bus['price']; ?></p>
                <p class="bus-desc">
                    <?php echo nl2br(htmlspecialchars($bus['description'])); ?>
                </p>
            </div>

            <div class="date-checker">
                <input type="date" name="date" id="check_date">
                <button type="button" id="checkBtn">
                    Check Availability
                </button>
                <input type="hidden" id="bus_id" value="<?php echo $bus['id']; ?>">
            </div>

            <div class="inquiry-box sidebar-inquiry">
                <h3>Bus Inquiry</h3>

                <form method="POST" id="userForm" novalidate>

                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                    <input type="hidden" name="bus_id" value="<?= $bus['id']; ?>">

                    <div class="form-group">
                        <input type="text" name="name" id="name" placeholder="Full Name">
                        <small class="error"></small>
                    </div>

                    <div class="form-group">
                        <input type="email" name="email" id="email" placeholder="Email (Optional)">
                        <small class="error"></small>
                    </div>

                    <div class="form-group">
                        <input type="text" name="phone" id="phone" placeholder="Phone">
                        <small class="error"></small>
                    </div>

                    <div class="form-group">
                        <textarea name="message" id="message" placeholder="Your Inquiry"></textarea>
                        <small class="error"></small>
                    </div>

                    <button type="submit" name="send_inquiry">Send Inquiry</button>
                </form>

            </div>

        </div>

    </div>
</section>

<script src="assets/js/inq-cnt-validation.js"></script>
<script src="assets/js/success-errorBox.js"></script>

<?php include 'includes/footer.php'; ?>

<script>
    function notify(message, type = "success") {
        const container = document.getElementById('notify');

        const box = document.createElement('div');
        box.className = `notify-box ${type}`;
        box.innerText = message;

        container.appendChild(box);

        // show animation
        setTimeout(() => box.classList.add('show'), 10);

        // auto remove
        setTimeout(() => {
            box.classList.remove('show');
            setTimeout(() => box.remove(), 300);
        }, 2500);
    }

    document.getElementById('checkBtn').addEventListener('click', function() {

        const date = document.getElementById('check_date').value;
        const busId = document.getElementById('bus_id').value;

        if (!date) {
            notify("Select a date first", "error");
            return;
        }

        fetch('check-date', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `bus_id=${busId}&date=${date}`
            })
            .then(res => res.text())
            .then(data => {
                if (data === "available") {
                    notify("Bus available on this date", "success");
                } else {
                    notify("No bus on this date", "error");
                }
            })
            .catch(() => {
                notify("Error checking availability", "error");
            });

    });
</script>