<div class="header-wrapper">
    <?php include 'includes/topbar.php'; ?>
    <?php include 'includes/navbar.php'; ?>
</div>

<section class="page-banner">
    <div class="overlay">
        <h1>My Bookings</h1>
        <p>You can view all your booking details here.</p>
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
                            <th>Status</th>
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
                                <td>
                                    <?php if ($row['payment_status'] == 'paid'): ?>
                                        <span class="badge success">Paid</span>
                                    <?php else: ?>
                                        <span class="badge pending">Pending</span>
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