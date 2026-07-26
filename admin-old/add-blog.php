<?php
include '../config/db.php';
include 'auth.php';
include '../includes/blog-functions.php';

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if (isset($_POST['submit'])) {

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

    $slug = generateSlug($title, $conn);

    if ($excerpt === '') {
        $excerpt = autoExcerpt($content, 160);
    }

    /* COVER IMAGE UPLOAD */
    $cover = '';
    if (!empty($_FILES['cover_image']['name'])) {
        $cover = time() . '_' . preg_replace('/[^A-Za-z0-9._-]/', '', $_FILES['cover_image']['name']);
        move_uploaded_file(
            $_FILES['cover_image']['tmp_name'],
            "uploads/images/blogs/" . $cover
        );
    }

    $stmt = $conn->prepare("
        INSERT INTO blogs
        (title, slug, category_id, author, cover_image, excerpt, content, tags, meta_title, meta_description, meta_keywords, is_featured, status)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "ssissssssssii",
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
        $status
    );

    if ($stmt->execute()) {
        $_SESSION['success'] = "Blog post added successfully.";
        header("Location: manage-blogs");
        exit();
    } else {
        $_SESSION['error'] = "Error adding blog post.";
        header("Location: add-blog");
        exit();
    }

    $stmt->close();
}

$categories = mysqli_query($conn, "SELECT * FROM blog_categories ORDER BY name ASC");

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="admin-content">
    <h2>Add New Blog</h2>

    <?php include 'includes/admin-alert.php'; ?>

    <form method="POST" enctype="multipart/form-data" class="admin-form validate-form">

        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

        <div class="form-group">
            <input type="text" name="title" id="title" placeholder="Blog Title" data-validate="name" required>
            <small class="error"></small>
        </div>

        <label>Category</label>
        <select name="category_id">
            <option value="">-- Select Category --</option>
            <?php mysqli_data_seek($categories, 0); while ($cat = mysqli_fetch_assoc($categories)): ?>
                <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
            <?php endwhile; ?>
        </select>

        <div class="form-group">
            <input type="text" name="author" placeholder="Author (default: DTP Team)">
        </div>

        <div class="form-group">
            <textarea name="excerpt" placeholder="Short excerpt / summary (leave blank to auto-generate, max 300 chars)" maxlength="300"></textarea>
            <small class="error"></small>
        </div>

        <div class="form-group">
            <label>Content *</label>
            <textarea name="content" id="content" placeholder="Write your blog content here (HTML allowed)" data-validate="text20" required rows="14"></textarea>
            <small class="error"></small>
        </div>

        <div class="form-group">
            <input type="text" name="tags" placeholder="Tags, comma separated (e.g. trekking, everest, guide)">
        </div>

        <div class="file_input">
            <label>Cover Image *</label>
            <input type="file" name="cover_image" accept="image/*" required>
        </div>

        <hr>
        <h3>SEO Settings</h3>

        <div class="form-group">
            <input type="text" name="meta_title" placeholder="Meta Title (max 70 chars, leave blank to auto-use post title)" maxlength="70">
        </div>

        <div class="form-group">
            <textarea name="meta_description" placeholder="Meta Description (max 160 chars, leave blank to auto-generate)" maxlength="160"></textarea>
        </div>

        <div class="form-group">
            <input type="text" name="meta_keywords" placeholder="Meta Keywords, comma separated">
        </div>

        <label>Featured?</label>
        <select name="is_featured">
            <option value="0">No</option>
            <option value="1">Yes</option>
        </select>

        <label>Status</label>
        <select name="status" required>
            <option value="1">Published</option>
            <option value="0">Draft</option>
        </select>

        <button name="submit">Publish Blog</button>
    </form>
</div>

<script src="assets/js/form-validator.js"></script>
<script src="assets/js/admin-alert.js"></script>

<?php include 'includes/footer.php'; ?>
