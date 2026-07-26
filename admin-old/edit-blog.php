<?php
include '../config/db.php';
include 'auth.php';
include '../includes/blog-functions.php';

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$id = (int)$_GET['id'];
$data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM blogs WHERE id=$id"));

if (!$data) {
    header("Location: manage-blogs");
    exit();
}

if (isset($_POST['update'])) {

    if (
        !isset($_POST['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
    ) {
        die("CSRF validation failed.");
    }

    $title            = trim($_POST['title']);
    $category_id      = !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null;
    $author           = trim($_POST['author']) ?: 'DTP Team';
    $excerpt          = trim($_POST['excerpt']);
    $content          = $_POST['content'];
    $tags             = trim($_POST['tags']);
    $meta_title       = trim($_POST['meta_title']);
    $meta_description = trim($_POST['meta_description']);
    $meta_keywords    = trim($_POST['meta_keywords']);
    $is_featured      = $_POST['is_featured'];
    $status           = $_POST['status'];

    /* Re-slug only if title changed, keep slug stable otherwise (best for SEO) */
    $slug = $data['slug'];
    if (strcasecmp($title, $data['title']) !== 0) {
        $slug = generateSlug($title, $conn, $id);
    }

    if ($excerpt === '') {
        $excerpt = autoExcerpt($content, 160);
    }

    /* COVER IMAGE UPLOAD (optional replace) */
    $cover = $data['cover_image'];
    if (!empty($_FILES['cover_image']['name'])) {
        $cover = time() . '_' . preg_replace('/[^A-Za-z0-9._-]/', '', $_FILES['cover_image']['name']);
        move_uploaded_file(
            $_FILES['cover_image']['tmp_name'],
            "uploads/images/blogs/" . $cover
        );
        if ($data['cover_image']) {
            @unlink("uploads/images/blogs/" . $data['cover_image']);
        }
    }

    $stmt = $conn->prepare("
        UPDATE blogs SET
        title = ?, slug = ?, category_id = ?, author = ?, cover_image = ?,
        excerpt = ?, content = ?, tags = ?, meta_title = ?, meta_description = ?,
        meta_keywords = ?, is_featured = ?, status = ?
        WHERE id = ?
    ");

    $stmt->bind_param(
        "ssissssssssiii",
        $title,
        $slug,
        $category_id,
        $author,
        $cover,
        $excerpt,
        $content,
        $tags,
        $meta_title,
        $meta_description,
        $meta_keywords,
        $is_featured,
        $status,
        $id
    );

    if ($stmt->execute()) {
        $_SESSION['success'] = "Blog post updated successfully.";
        header("Location: manage-blogs");
        exit();
    } else {
        $_SESSION['error'] = "Error updating blog post.";
        header("Location: edit-blog?id=$id");
        exit();
    }

    $stmt->close();
}

$categories = mysqli_query($conn, "SELECT * FROM blog_categories ORDER BY name ASC");

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="admin-content">
    <h2>Edit Blog Post</h2>

    <?php include 'includes/admin-alert.php'; ?>

    <form method="POST" enctype="multipart/form-data" class="admin-form validate-form">

        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

        <div class="form-group">
            <input type="text" name="title" id="title" placeholder="Blog Title" data-validate="name"
                value="<?= htmlspecialchars($data['title']) ?>" required>
            <small class="error"></small>
        </div>

        <label>Category</label>
        <select name="category_id">
            <option value="">-- Select Category --</option>
            <?php mysqli_data_seek($categories, 0); while ($cat = mysqli_fetch_assoc($categories)): ?>
                <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $data['category_id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['name']) ?>
                </option>
            <?php endwhile; ?>
        </select>

        <div class="form-group">
            <input type="text" name="author" placeholder="Author" value="<?= htmlspecialchars($data['author']) ?>">
        </div>

        <div class="form-group">
            <textarea name="excerpt" placeholder="Short excerpt / summary" maxlength="300"><?= htmlspecialchars($data['excerpt']) ?></textarea>
            <small class="error"></small>
        </div>

        <div class="form-group">
            <label>Content *</label>
            <textarea name="content" id="content" data-validate="text20" required rows="14"><?= htmlspecialchars($data['content']) ?></textarea>
            <small class="error"></small>
        </div>

        <div class="form-group">
            <input type="text" name="tags" placeholder="Tags, comma separated" value="<?= htmlspecialchars($data['tags']) ?>">
        </div>

        <div class="file_input">
            <label>Cover Image</label>
            <?php if ($data['cover_image']): ?>
                <img src="uploads/images/blogs/<?= htmlspecialchars($data['cover_image']) ?>" height="60" style="display:block;margin-bottom:8px;">
            <?php endif; ?>
            <input type="file" name="cover_image" accept="image/*">
            <small>Leave empty to keep current image.</small>
        </div>

        <hr>
        <h3>SEO Settings</h3>

        <div class="form-group">
            <input type="text" name="meta_title" placeholder="Meta Title" maxlength="70" value="<?= htmlspecialchars($data['meta_title']) ?>">
        </div>

        <div class="form-group">
            <textarea name="meta_description" placeholder="Meta Description" maxlength="160"><?= htmlspecialchars($data['meta_description']) ?></textarea>
        </div>

        <div class="form-group">
            <input type="text" name="meta_keywords" placeholder="Meta Keywords, comma separated" value="<?= htmlspecialchars($data['meta_keywords']) ?>">
        </div>

        <label>Featured?</label>
        <select name="is_featured">
            <option value="0" <?= $data['is_featured'] == 0 ? 'selected' : '' ?>>No</option>
            <option value="1" <?= $data['is_featured'] == 1 ? 'selected' : '' ?>>Yes</option>
        </select>

        <label>Status</label>
        <select name="status" required>
            <option value="1" <?= $data['status'] == 1 ? 'selected' : '' ?>>Published</option>
            <option value="0" <?= $data['status'] == 0 ? 'selected' : '' ?>>Draft</option>
        </select>

        <button name="update">Update Blog Post</button>
    </form>
</div>

<script src="assets/js/form-validator.js"></script>
<script src="assets/js/admin-alert.js"></script>

<?php include 'includes/footer.php'; ?>
