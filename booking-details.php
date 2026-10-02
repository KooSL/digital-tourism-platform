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
$slug = $_GET['trip'] ?? '';
$booking_id = isset($_GET['booking_id']) ? (int)$_GET['booking_id'] : null;

$stmt = mysqli_prepare($conn, "SELECT * FROM trips WHERE slug=? AND status=1");
mysqli_stmt_bind_param($stmt, "s", $slug);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$trip = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);
$p_id = $trip['id'] ?? null;

$stmt = mysqli_prepare($conn, "SELECT * FROM package_bookings WHERE package_id=? AND user_id=? AND id=?");
mysqli_stmt_bind_param($stmt, "iii", $p_id, $user_id, $booking_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$pb = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);
$booking_id = $pb['id'] ?? null;

$stmt = $conn->prepare("
    SELECT pb.*, t.title, t.slug AS _slug, t.banner_image, t.duration, t.type
    FROM package_bookings pb
    JOIN trips t ON pb.package_id = t.id
    WHERE pb.id = ? AND pb.user_id = ?
");
$stmt->bind_param("ii", $booking_id, $user_id);
$stmt->execute();
$booking = $stmt->get_result()->fetch_assoc();

if (!$booking || $booking['_slug'] !== $slug) {
    header("Location: my-bookings?error=not_found");
    exit;
}

$returnTo = "booking-details?trip=" . urlencode($slug) . "&booking_id=" . $booking_id;

/* ---- Small display helpers ---- */
function bdDate($value, $withTime = false)
{
    $ts = strtotime((string)$value);
    if (!$ts) {
        return '-';
    }
    return date($withTime ? 'd M Y, h:i A' : 'd M Y', $ts);
}

// Booking status -> [css class, label]
$statusMap = [
    'pending'  => ['pending', 'Pending'],
    'canceled' => ['danger', 'Canceled'],
];
[$statusClass, $statusLabel] = $statusMap[$booking['status']] ?? ['success', 'Confirmed'];

// Payment status -> [css class, label]
$paymentMap = [
    'full'    => ['success', 'Fully Paid'],
    'deposit' => ['pending', 'Deposit Paid'],
];
[$paymentClass, $paymentLabel] = $paymentMap[$booking['payment_status']] ?? ['pending', 'Pending'];
?>

<link rel="stylesheet" href="assets/css/booking-details.css">

<div class="header-wrapper">
    <?php include 'includes/topbar.php'; ?>
    <?php include 'includes/navbar.php'; ?>
</div>

<section class="page-banner">
    <div class="overlay">
        <h1>Booking Details</h1>
        <p>Full details of your booking for <?= htmlspecialchars($booking['title']) ?>.</p>
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
                'not_found'     => "Booking not found.",
                'cannot_cancel' => "This booking can no longer be canceled.",
            ];
            echo $errorMsgs[$_GET['error']] ?? "Something went wrong. Please try again.";
            ?>
        </div>
    <?php endif; ?>
</section>

<section class="bd-section">
    <div class="container bd-grid">

        <!-- LEFT: IMAGE -->
        <div class="bd-media">
            <div class="bd-image">
                <img src="uploads/images/trips/<?= htmlspecialchars($booking['banner_image']) ?>"
                    alt="<?= htmlspecialchars($booking['title']) ?>">
                <div class="bd-image-status">
                    <span class="bd-badge <?= $statusClass ?>"><?= $statusLabel ?></span>
                </div>
            </div>
        </div>

        <!-- RIGHT: DETAILS -->
        <div class="bd-content">

            <h2 class="bd-title"><?= htmlspecialchars($booking['title']) ?></h2>

            <ul class="bd-meta">
                <?php if (!empty($booking['duration'])): ?>
                    <li><i class="fa-regular fa-clock"></i> <?= htmlspecialchars($booking['duration']) ?></li>
                <?php endif; ?>
                <?php if (!empty($booking['type'])): ?>
                    <li><i class="fa-solid fa-route"></i> <?= htmlspecialchars(ucfirst($booking['type'])) ?></li>
                <?php endif; ?>
            </ul>

            <?php
            if ($booking['payment_status'] === 'deposit') { ?>
                <div class="note booking-details-note-top">
                    You have paid a deposit for this booking. Please pay the remaining amount before the travel date.
                </div>
            <?php } elseif ($booking['payment_status'] === 'pending') { ?>
                <div class="note booking-details-note-top">
                    Your payment is pending. Please complete the payment to confirm your booking.
                </div>
            <?php } ?>

            <dl class="bd-details">
                <div class="bd-row">
                    <dt>Booking date</dt>
                    <dd><?= htmlspecialchars(bdDate($booking['created_at'], true)) ?></dd>
                </div>
                <div class="bd-row">
                    <dt>Travel date</dt>
                    <dd><?= htmlspecialchars(bdDate($booking['travel_date'])) ?></dd>
                </div>
                <div class="bd-row">
                    <dt>Persons</dt>
                    <dd><?= (int)$booking['persons'] ?></dd>
                </div>
                <div class="bd-row">
                    <dt>Total Amount</dt>
                    <dd><?= (int)$booking['total_amount'] ?></dd>
                </div>

                <?php
                if ($booking['payment_status'] === 'deposit') { ?>
                    <div class="bd-row">
                        <dt>Deposit Amount</dt>
                        <dd><?= (int)$booking['amount_paid'] ?></dd>
                    </div>
                <?php } ?>

                <div class="bd-row">
                    <dt>Remaining Amount</dt>
                    <dd><?= (int)$booking['total_amount'] - (int)$booking['amount_paid'] ?></dd>
                </div>

                <div class="bd-row">
                    <dt>Transaction ID</dt>
                    <dd class="bd-mono"><?= htmlspecialchars($booking['transaction_id'] ?: '-') ?></dd>
                </div>
                <div class="bd-row">
                    <dt>Payment status</dt>
                    <dd><span class="bd-badge <?= $paymentClass ?>"><?= $paymentLabel ?></span></dd>
                </div>

                <div class="bd-row">
                    <dt>Booking status</dt>
                    <dd><span class="bd-badge <?= $statusClass ?>"><?= $statusLabel ?></span></dd>
                </div>
            </dl>

            <div class="pay-qr">
                <?php if ($booking['payment_status'] === 'pending'): ?>
                    <div class="bd-qr">
                        <h3>Pay via QR Code</h3>
                        <img src="assets/images/qr-code.png" alt="QR Code for payment">
                        <p>Scan this QR code to complete your payment.</p>
                    </div>
                <?php endif; ?>

                <?php if ($booking['payment_status'] === 'deposit'): ?>
                    <div class="bd-qr">
                        <h3>Pay Remaining Amount via QR Code</h3>

                        <div class="qr-buttons">
                            <button type="button" class="bd-btn primary qr" id="show_qr_esewa">
                                eSewa
                            </button>

                            <button type="button" class="bd-btn primary qr" id="show-qr_banking">
                                Mobile Banking
                            </button>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <div id="qr_modal_esewa" class="qr-modal-overlay">
                <div class="qr-modal-box">
                    <button type="button" class="qr-modal-close">&times;</button>

                    <img src="assets/images/qrcodes/esewa.jpg"
                        alt="Payment QR"
                        style="width:260px;height:260px;object-fit:contain;border:1px solid #e5e8f0;border-radius:8px;padding:10px;background:#fff;">

                    <p class="text-muted" style="margin-top:10px;">
                        Remaining: Rs.
                        <?= (int)$booking['total_amount'] - (int)$booking['amount_paid'] ?>
                    </p>
                </div>
            </div>

            <div id="qr_modal_banking" class="qr-modal-overlay">
                <div class="qr-modal-box">
                    <button type="button" class="qr-modal-close">&times;</button>

                    <img src="assets/images/qrcodes/banking.png"
                        alt="Payment QR"
                        style="width:260px;height:260px;object-fit:contain;border:1px solid #e5e8f0;border-radius:8px;padding:10px;background:#fff;">

                    <p class="text-muted" style="margin-top:10px;">
                        Remaining: Rs.
                        <?= (int)$booking['total_amount'] - (int)$booking['amount_paid'] ?>
                    </p>
                </div>
            </div>

            <?php if (!empty($booking['message'])): ?>
                <div class="bd-notes">
                    <h3>Special requests</h3>
                    <p><?= nl2br(htmlspecialchars($booking['message'])) ?></p>
                </div>
            <?php endif; ?>

            <div class="note booking-details-note-bottom">
                If you pay the remaining amount, please inform us to update your booking status and If you have any questions regarding your booking, please contact us.
            </div>

            <div class="bd-actions">
                <a href="my-bookings" class="bd-btn outline">
                    <i class="fa-solid fa-arrow-left"></i> My bookings
                </a>

                <a href="trip-details?trip=<?= urlencode($booking['_slug']) ?>&type=<?= urlencode($trip['type']) ?>" class="bd-btn primary">
                    View package
                </a>

                <?php if ($booking['payment_status'] === 'pending' && $booking['status'] !== 'canceled'): ?>
                    <form method="POST" action="cancel-booking" class="inline-cancel-form"
                        onsubmit="return showConfirm(this, 'Cancel this booking? This cannot be undone.')">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                        <input type="hidden" name="booking_id" value="<?= (int)$booking['id'] ?>">
                        <input type="hidden" name="return_to" value="<?= htmlspecialchars($returnTo) ?>">
                        <button type="submit" class="bd-btn danger">Cancel booking</button>
                    </form>
                <?php endif; ?>
            </div>

        </div>

    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        const showEsewa = document.getElementById('show_qr_esewa');
        const showBanking = document.getElementById('show-qr_banking');

        const esewaModal = document.getElementById('qr_modal_esewa');
        const bankingModal = document.getElementById('qr_modal_banking');

        // Open eSewa modal
        if (showEsewa && esewaModal) {
            showEsewa.addEventListener('click', function() {
                esewaModal.classList.add('active');
            });
        }

        // Open Mobile Banking modal
        if (showBanking && bankingModal) {
            showBanking.addEventListener('click', function() {
                bankingModal.classList.add('active');
            });
        }

        // Close buttons
        document.querySelectorAll('.qr-modal-close').forEach(function(button) {
            button.addEventListener('click', function() {
                const modal = button.closest('.qr-modal-overlay');

                if (modal) {
                    modal.classList.remove('active');
                }
            });
        });

        // Close when clicking outside the modal box
        document.querySelectorAll('.qr-modal-overlay').forEach(function(modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    modal.classList.remove('active');
                }
            });
        });

    });
</script>

<?php include 'includes/footer.php'; ?>