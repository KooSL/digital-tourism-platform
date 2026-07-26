<?php
include '../config/db.php';
include 'auth.php';

$limit = 10;

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max($page, 1);

$offset = ($page - 1) * $limit;

// Total users
$totalResult = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total FROM users"
);

$totalRows = mysqli_fetch_assoc($totalResult)['total'];
$totalPages = ceil($totalRows / $limit);


if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    if (mysqli_query($conn, "DELETE FROM trip_reviews WHERE id=$id")) {
        $_SESSION['success'] = "Review deleted successfully.";
    } else {
        $_SESSION['error'] = "Failed to delete review.";
    }
    header("Location: reviews");
    exit;
}
?>

<?php

// Approve review
if (isset($_GET['approve'])) {

    $id = intval($_GET['approve']);

    mysqli_query(
        $conn,
        "UPDATE trip_reviews 
         SET status = 1 
         WHERE id = $id"
    );

    $_SESSION['success'] = "Review Approved successfully.";
    header("Location: reviews");
    exit;
}



// Reject review
if (isset($_GET['reject'])) {

    $id = intval($_GET['reject']);

    mysqli_query(
        $conn,
        "UPDATE trip_reviews 
         SET status = 0 
         WHERE id = $id"
    );

    $_SESSION['success'] = "Review Rejected successfully.";
    header("Location: reviews");
    exit;
}

?>

<?php
include 'includes/header.php';
include 'includes/sidebar.php';
?>



<div class="admin-content">
    <h2>Ratings and Reviews</h2>

    <?php include 'includes/admin-alert.php'; ?>

    <table class="admin-table">
        <thead>
            <tr>
                <th>S.N.</th>
                <th>Created Date</th>
                <th>Trip Name</th>
                <th>Name</th>
                <th>Rating</th>
                <th>Review</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>

        <tbody>
            <?php
            $i = $offset + 1;

            $result = mysqli_query(
                $conn,
                "SELECT * FROM trip_reviews
     ORDER BY id DESC
     LIMIT $limit OFFSET $offset"
            );
            while ($row = mysqli_fetch_assoc($result)) {
            ?>
                <tr>
                    <td><?= $i++ ?></td>
                    <td><?= htmlspecialchars($row['created_at']) ?></td>
                    <td><?= htmlspecialchars($row['trip_id']) ?></td>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= htmlspecialchars($row['rating']) ?></td>
                    <td><?= htmlspecialchars($row['review']) ?></td>
                    <td><?= htmlspecialchars($row['status']) ?></td>
                    <td class="action-col">

                        <?php if ($row['status'] == 0) { ?>

                            <a href="?approve=<?= $row['id'] ?>"
                                class="btn-approve"
                                onclick="return confirm('Approve this Review?')">
                                Approve
                            </a>


                        <?php } else { ?>

                            <a href="?reject=<?= $row['id'] ?>"
                                class="btn-reject"
                                onclick="return confirm('Reject this Review?')">
                                Reject
                            </a>

                        <?php } ?>

                        <a href="javascript:void(0)"
                            onclick="showConfirm('?delete=<?= $row['id'] ?>','Delete this review?')"
                            class="btn-delete">
                            Delete
                        </a>

                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <?php include 'includes/admin-pagination.php'; ?>

</div>

<script src="assets/js/admin-alert.js"></script>

<?php include 'includes/footer.php'; ?>
<script src="../assets/js/confirmation.js"></script>
