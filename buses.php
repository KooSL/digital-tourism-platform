<?php include 'includes/header.php'; ?>
<div class="header-wrapper">
    <?php include 'includes/topbar.php'; ?>
    <?php include 'includes/navbar.php'; ?>
</div>

<?php include 'config/db.php'; ?>

<section class="page-banner">
    <div class="overlay">
        <h1>Bus Bookings</h1>
        <p>Explore our buses with best fare deals with professional assistance</p>
    </div>
</section>

<section class="home-buses">
    <div class="container">

        <h2 class="section-title-bus">Available Buses</h2>

        <div class="bus-grid">

            <?php
            $query = mysqli_query(
                $conn,
                "SELECT * FROM buses WHERE status = 1 ORDER BY id DESC"
            );

            if (mysqli_num_rows($query) > 0) {
                while ($bus = mysqli_fetch_assoc($query)) {
            ?>
                    <div class="bus-card">
                        <div class="bus-card-img">

                            <!-- <?php if ($bus['is_group_fare'] == 1): ?>
                <span class="group-fare-badge">
                  <i class="fa-solid fa-users"></i> Group Fare
                </span>
              <?php endif; ?> -->

                            <img src="admin/uploads/images/buses/<?= $bus['banner_image']; ?>" alt="<?= htmlspecialchars($bus['from_location'] . ' to ' . $bus['to_location']); ?>">

                        </div>

                        <div class="bus-info">
                            <h3>
                                <?= htmlspecialchars($bus['from_location']); ?> →
                                <?= htmlspecialchars($bus['to_location']); ?>
                            </h3>

                            <a href="bus-details?id=<?= $bus['id']; ?>" class="btn-primary">
                                View Details
                            </a>
                        </div>
                    </div>
            <?php
                }
            } else {
                echo "<p style='text-align:center;'>No buses available at the moment.</p>";
            }
            ?>

        </div>

    </div>
</section>

<?php include 'includes/footer.php'; ?>