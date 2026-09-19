<?php
$pageTitle = "Booking Details";
include 'includes/header.php';

include 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: signin");
    exit;
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$user_id = $_SESSION['user_id'];
// $booking_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$slug = $_GET['slug'] ?? '';

$stmt = mysqli_prepare($conn, "SELECT * FROM tours WHERE slug=? AND status=1");
mysqli_stmt_bind_param($stmt, "s", $slug);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$tour = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);
$p_id = $tour['id'] ?? null;

$stmt = mysqli_prepare($conn, "SELECT * FROM package_bookings WHERE package_id=? AND user_id=?");
mysqli_stmt_bind_param($stmt, "si", $p_id, $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$pb = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);
$booking_id = $pb['id'] ?? null;

$stmt = $conn->prepare("
    SELECT pb.*, t.title, t.slug AS tour_slug, t.banner_image, t.duration, t.type
    FROM package_bookings pb
    JOIN tours t ON pb.package_id = t.id
    WHERE pb.id = ? AND pb.user_id = ?
");
$stmt->bind_param("ii", $booking_id, $user_id);
$stmt->execute();
$booking = $stmt->get_result()->fetch_assoc();

if (!$booking || $booking['tour_slug'] !== $slug) {
    header("Location: my-bookings?error=not_found");
    exit;
}

$returnTo = "booking-details?slug=" . urlencode($slug);
?>

<div class="header-wrapper">
    <?php include 'includes/topbar.php'; ?>
    <?php include 'includes/navbar.php'; ?>
</div>

<section class="page-banner">
    <div class="overlay">
        <h1>Booking Details</h1>
        <p>Full details of your booking for <?= htmlspecialchars($booking['title']) ?>. <?php echo htmlspecialchars($booking_id); ?></p>
    </div>

    <?php if (isset($_GET['success']) && $_GET['success'] === 'canceled'): ?>
        <div class="success-box" id="successBox">
            <strong>Success!</strong> Your booking has been canceled.
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="error-box" id="errorBox">
            <?php
            $errorMsgs = [
                'not_found'  => "Booking not found.",
                'cannot_cancel' => "This booking can no longer be canceled.",
            ];
            echo $errorMsgs[$_GET['error']] ?? "Something went wrong. Please try again.";
            ?>
        </div>
    <?php endif; ?>
</section>

<section class="table-section">
    <div class="container">

        <div class="booking-details-box">

            <div class="booking-details-header">
                <img src="uploads/images/tours/<?= htmlspecialchars($booking['banner_image']) ?>" alt="<?= htmlspecialchars($booking['title']) ?>">
                <div>
                    <h2><?= htmlspecialchars($booking['title']) ?></h2>
                    <p><?= htmlspecialchars($booking['duration'] ?? '') ?> &middot; <?= htmlspecialchars(ucfirst($booking['type'] ?? '')) ?></p>
                    <a href="tour-details?slug=<?= urlencode($booking['tour_slug']) ?>" class="btn view">View Package</a>
                </div>
            </div>

            <div class="booking-details-grid">
                <div>
                    <span>Booking Date</span>
                    <p><?= htmlspecialchars($booking['created_at']) ?></p>
                </div>
                <div>
                    <span>Travel Date</span>
                    <p><?= htmlspecialchars($booking['travel_date']) ?></p>
                </div>
                <div>
                    <span>Persons</span>
                    <p><?= htmlspecialchars($booking['persons']) ?></p>
                </div>
                <div>
                    <span>Transaction ID</span>
                    <p><?= htmlspecialchars($booking['transaction_id'] ?? '-') ?></p>
                </div>
                <div>
                    <span>Payment Status</span>
                    <p>
                        <?php if ($booking['payment_status'] == 'paid'): ?>
                            <span class="badge success">Paid</span>
                        <?php elseif ($booking['payment_status'] == 'partial'): ?>
                            <span class="badge pending">Partially Paid</span>
                        <?php else: ?>
                            <span class="badge pending">Pending</span>
                        <?php endif; ?>
                    </p>
                </div>
                <div>
                    <span>Booking Status</span>
                    <p>
                        <?php if ($booking['status'] == 'pending'): ?>
                            <span class="badge pending">Pending</span>
                        <?php elseif ($booking['status'] == 'canceled'): ?>
                            <span class="badge danger">Canceled</span>
                        <?php else: ?>
                            <span class="badge success">Confirmed</span>
                        <?php endif; ?>
                    </p>
                </div>
            </div>

            <?php if (!empty($booking['special_requests'])): ?>
                <div class="booking-notes">
                    <span>Special Requests</span>
                    <p><?= nl2br(htmlspecialchars($booking['special_requests'])) ?></p>
                </div>
            <?php endif; ?>

            <div class="table-actions">
                <a href="my-bookings" class="btn view">Back to My Bookings</a>

                <?php if ($booking['payment_status'] === 'pending' && $booking['status'] !== 'canceled'): ?>
                    <form method="POST" action="cancel-booking" class="inline-cancel-form"
                        onsubmit="return showConfirm(this, 'Cancel this booking? This cannot be undone.')">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                        <input type="hidden" name="booking_id" value="<?= (int)$booking['id'] ?>">
                        <input type="hidden" name="return_to" value="<?= htmlspecialchars($returnTo) ?>">
                        <button type="submit" class="btn cancel">Cancel Booking</button>
                    </form>
                <?php endif; ?>
            </div>

        </div>

    </div>
</section>

<?php include 'includes/footer.php'; ?>