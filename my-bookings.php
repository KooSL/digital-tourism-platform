<?php
include 'includes/header.php';

include 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: signin");
    exit;
}

$user_id = $_SESSION['user_id'];

$limit = 2;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max($page, 1);

$offset = ($page - 1) * $limit;

$stmt = $conn->prepare("
    SELECT pb.*, t.title
    FROM package_bookings pb
    JOIN tours t ON pb.package_id = t.id
    WHERE pb.user_id = ?
    ORDER BY pb.id DESC
    LIMIT ? OFFSET ?
");
$stmt->bind_param("iii", $user_id, $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();

$total_stmt = $conn->prepare("
    SELECT COUNT(*) as total 
    FROM package_bookings 
    WHERE user_id = ?
");
$total_stmt->bind_param("i", $user_id);
$total_stmt->execute();
$total_result = $total_stmt->get_result()->fetch_assoc();

$total_pages = ceil($total_result['total'] / $limit);
?>


<div class="header-wrapper">
    <?php include 'includes/topbar.php'; ?>
    <?php include 'includes/navbar.php'; ?>
</div>

<section class="page-banner">
    <div class="overlay">
        <h1>My Bookings</h1>
        <p>You can view all your booking details here.</p>
    </div>

    <div class="container">

        <div class="filter-wrapper">

            <!-- SEARCH -->
            <form method="GET" class="search-bar">
                <input type="text" name="q" placeholder="Search bookings..."
                    value="<?= $_GET['q'] ?? '' ?>" required>
                <button type="submit"><i class="fa fa-search"></i></button>
            </form>

            <!-- FILTER -->
            <div class="filter-dropdown">

                <button type="button" id="filterToggle" class="filter-btn">
                    <i class="fa fa-sliders"></i> Filters
                </button>

                <form method="GET" class="filter-box" id="filterBox">

                    <input type="hidden" name="q" value="<?= $_GET['q'] ?? '' ?>">

                    <div class="filter-group">
                        <select name="type">
                            <option value="">All Types</option>
                            <option value="domestic">Domestic</option>
                            <option value="international">International</option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <input type="number" name="price" placeholder="Max Price">
                    </div>

                    <div class="filter-group small">
                        <label><input type="checkbox" name="popular"> Popular</label>
                        <label><input type="checkbox" name="latest"> Latest</label>
                    </div>

                    <button type="submit" class="apply-btn">Apply</button>

                </form>

            </div>

        </div>

</section>

<section class="table-section">

    <div class="container">

        <?php if ($result->num_rows > 0): ?>


            <div class="table-container">
                <table class="table">

                    <thead class="table-head">
                        <tr>
                            <th>S.N.</th>
                            <th>Booking Date</th>
                            <th>Package</th>
                            <th>Travel Date</th>
                            <th>Persons</th>
                            <th>Transaction ID</th>
                            <th>Payment Status</th>
                            <th>Booking Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody class="table-body">
                        <?php $i = 1; ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $i++; ?></td>
                                <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                                <td><?php echo htmlspecialchars($row['title']); ?></td>
                                <td><?php echo htmlspecialchars($row['travel_date']); ?></td>
                                <td><?php echo htmlspecialchars($row['persons']); ?></td>
                                <td><?php echo htmlspecialchars($row['transaction_id']); ?></td>
                                <td>
                                    <?php if ($row['payment_status'] == 'paid'): ?>
                                        <span class="badge success">Paid</span>
                                    <?php else: ?>
                                        <span class="badge pending">Pending</span>
                                    <?php endif; ?>
                                </td>
                                <!-- <td><?php echo htmlspecialchars($row['status']); ?></td> -->
                                <td>
                                    <?php if ($row['status'] == 'pending'): ?>
                                        <span class="badge pending">Pending</span>
                                    <?php elseif ($row['status'] == 'canceled'):  ?>
                                        <span class="badge danger">Canceled</span>
                                    <?php else: ?>
                                        <span class="badge success">Confirmed</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="table-actions">

                                        <a href="booking-details?id=<?php echo $row['id']; ?>" class="btn view">
                                            View
                                        </a>

                                        <?php if ($row['payment_status'] != 'paid'): ?>
                                            <a href="cancel-booking?id=<?php echo $row['id']; ?>" class="btn cancel">
                                                Cancel
                                            </a>
                                        <?php endif; ?>

                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>

                </table>
            </div>

            <div class="pagination">
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?page=<?php echo $i; ?>"
                        class="<?php echo ($i == $page) ? 'active' : ''; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>
            </div>

        <?php else: ?>

            <p class="no-bookings">No bookings found.</p>

    </div>

<?php endif; ?>

</div>

</section>

<script>
    const btn = document.getElementById("filterToggle");
    const box = document.getElementById("filterBox");

    btn.onclick = () => {
        box.classList.toggle("active");
    };

    document.addEventListener("click", (e) => {
        if (!btn.contains(e.target) && !box.contains(e.target)) {
            box.classList.remove("active");
        }
    });
</script>

<?php include 'includes/footer.php'; ?>