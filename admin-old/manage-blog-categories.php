<?php
include '../config/db.php';
include 'auth.php';
include '../includes/blog-functions.php';

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// ADD CATEGORY
if (isset($_POST['submit'])) {

    if (
        !isset($_POST['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
    ) {
        die("CSRF validation failed.");
    }

    $name = trim($_POST['name']);

    if ($name !== '') {
        $slug = strtolower(trim(preg_replace('/[^a-z0-9]+/', '-', strtolower($name)), '-'));

        $stmt = $conn->prepare("INSERT INTO blog_categories (name, slug) VALUES (?, ?)");
        $stmt->bind_param("ss", $name, $slug);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Category added successfully.";
        } else {
            $_SESSION['error'] = "Category already exists or could not be added.";
        }
    }

    header("Location: manage-blog-categories");
    exit();
}

// DELETE CATEGORY
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM blog_categories WHERE id=$id");
    $_SESSION['success'] = "Category deleted. Posts in it are now uncategorized.";
    header("Location: manage-blog-categories");
    exit();
}

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="admin-content">
    <h2>Manage Blog Categories</h2>

    <?php include 'includes/admin-alert.php'; ?>

    <form method="POST" class="admin-form" style="max-width:400px;margin-bottom:25px;">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <div class="form-group">
            <input type="text" name="name" placeholder="New category name" required>
        </div>
        <button name="submit">Add Category</button>
    </form>

    <table class="admin-table">
        <thead>
            <tr>
                <th>S.N.</th>
                <th>Name</th>
                <th>Slug</th>
                <th>Posts</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = 1;
            $result = mysqli_query($conn, "
                SELECT c.*, (SELECT COUNT(*) FROM blogs b WHERE b.category_id = c.id) AS post_count
                FROM blog_categories c
                ORDER BY c.name ASC
            ");
            while ($row = mysqli_fetch_assoc($result)) {
            ?>
                <tr>
                    <td><?= $i++ ?></td>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= htmlspecialchars($row['slug']) ?></td>
                    <td><?= (int)$row['post_count'] ?></td>
                    <td class="action-col">
                        <a href="javascript:void(0)"
                            onclick="showConfirm('?delete=<?= $row['id'] ?>','Delete this category?')"
                            class="btn-delete">
                            Delete
                        </a>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<script src="assets/js/admin-alert.js"></script>
<?php include 'includes/footer.php'; ?>
<script src="../assets/js/confirmation.js"></script>
