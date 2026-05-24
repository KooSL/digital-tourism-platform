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

    <div class="container">

        <div class="filter-wrapper">

            <!-- SEARCH -->
            <form method="GET" class="search-bar">
                <input type="text" name="q" placeholder="Search buses..."
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