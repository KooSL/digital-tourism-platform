<?php
include '../config/db.php';
include 'auth.php';
include '../includes/blog-functions.php';

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

/* DELETE */
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $img = mysqli_fetch_assoc(mysqli_query($conn, "SELECT cover_image FROM blogs WHERE id=$id"));
    if ($img && $img['cover_image']) {
        @unlink("uploads/images/blogs/" . $img['cover_image']);
    }
    if (mysqli_query($conn, "DELETE FROM blogs WHERE id=$id")) {
        $_SESSION['success'] = "Blog post deleted successfully.";
    } else {
        $_SESSION['error'] = "Failed to delete blog post.";
    }
    header("Location: manage-blogs" . (isset($_GET['qs']) ? '?' . $_GET['qs'] : ''));
    exit;
}

/* ADD */
if (isset($_POST['submit'])) {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
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
    $is_featured      = (int)$_POST['is_featured'];
    $status           = (int)$_POST['status'];

    if ($title === '' || $content === '' || empty($_FILES['cover_image']['name'])) {
        $_SESSION['error'] = "Title, content and cover image are required.";
        $_SESSION['reopen_modal'] = 'addModal';
    } else {
        $slug = generateSlug($title, $conn);
        if ($excerpt === '') $excerpt = autoExcerpt($content, 160);

        $cover = time() . '_' . preg_replace('/[^A-Za-z0-9._-]/', '', $_FILES['cover_image']['name']);
        move_uploaded_file($_FILES['cover_image']['tmp_name'], "uploads/images/blogs/" . $cover);

        $stmt = $conn->prepare("
            INSERT INTO blogs
            (title, slug, category_id, author, cover_image, excerpt, content, tags, meta_title, meta_description, meta_keywords, is_featured, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param(
            "ssissssssssii",
            $title, $slug, $category_id, $author, $cover, $excerpt, $content, $tags,
            $meta_title, $meta_description, $meta_keywords, $is_featured, $status
        );

        if ($stmt->execute()) {
            $_SESSION['success'] = "Blog post added successfully.";
        } else {
            $_SESSION['error'] = "Error adding blog post.";
        }
        $stmt->close();
    }

    header("Location: manage-blogs" . (!empty($_POST['qs']) ? '?' . $_POST['qs'] : ''));
    exit();
}

/* UPDATE */
if (isset($_POST['update'])) {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("CSRF validation failed.");
    }

    $id = (int)$_POST['id'];
    $data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM blogs WHERE id=$id"));

    if ($id <= 0 || !$data) {
        $_SESSION['error'] = "Invalid blog post.";
        $_SESSION['reopen_modal'] = 'editModal';
        header("Location: manage-blogs" . (!empty($_POST['qs']) ? '?' . $_POST['qs'] : ''));
        exit();
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
    $is_featured      = (int)$_POST['is_featured'];
    $status           = (int)$_POST['status'];

    if ($title === '' || $content === '') {
        $_SESSION['error'] = "Title and content are required.";
        $_SESSION['reopen_modal'] = 'editModal';
    } else {
        $slug = $data['slug'];
        if (strcasecmp($title, $data['title']) !== 0) {
            $slug = generateSlug($title, $conn, $id);
        }
        if ($excerpt === '') $excerpt = autoExcerpt($content, 160);

        $cover = $data['cover_image'];
        if (!empty($_FILES['cover_image']['name'])) {
            $cover = time() . '_' . preg_replace('/[^A-Za-z0-9._-]/', '', $_FILES['cover_image']['name']);
            move_uploaded_file($_FILES['cover_image']['tmp_name'], "uploads/images/blogs/" . $cover);
            if ($data['cover_image']) @unlink("uploads/images/blogs/" . $data['cover_image']);
        }

        $stmt = $conn->prepare("
            UPDATE blogs SET
            title=?, slug=?, category_id=?, author=?, cover_image=?,
            excerpt=?, content=?, tags=?, meta_title=?, meta_description=?,
            meta_keywords=?, is_featured=?, status=?
            WHERE id=?
        ");
        $stmt->bind_param(
            "ssissssssssiii",
            $title, $slug, $category_id, $author, $cover, $excerpt, $content, $tags,
            $meta_title, $meta_description, $meta_keywords, $is_featured, $status, $id
        );

        if ($stmt->execute()) {
            $_SESSION['success'] = "Blog post updated successfully.";
        } else {
            $_SESSION['error'] = "Failed to update blog post.";
        }
        $stmt->close();
    }

    header("Location: manage-blogs" . (!empty($_POST['qs']) ? '?' . $_POST['qs'] : ''));
    exit();
}

/* SEARCH + FILTER + PAGINATION */
$search  = isset($_GET['search']) ? trim($_GET['search']) : '';
$status  = isset($_GET['status']) && $_GET['status'] !== '' ? (int)$_GET['status'] : null;
$catId   = isset($_GET['category']) && $_GET['category'] !== '' ? (int)$_GET['category'] : null;
$featured = isset($_GET['featured']) && $_GET['featured'] !== '' ? (int)$_GET['featured'] : null;

$where = [];
$types = '';
$params = [];

if ($search !== '') {
    $where[] = "(b.title LIKE ? OR b.author LIKE ? OR b.tags LIKE ?)";
    $like = "%$search%";
    $types .= 'sss';
    $params[] = $like; $params[] = $like; $params[] = $like;
}
if ($status !== null) {
    $where[] = "b.status = ?";
    $types .= 'i';
    $params[] = $status;
}
if ($catId !== null) {
    $where[] = "b.category_id = ?";
    $types .= 'i';
    $params[] = $catId;
}
if ($featured !== null) {
    $where[] = "b.is_featured = ?";
    $types .= 'i';
    $params[] = $featured;
}
$whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

$limit = 8;
$page = isset($_GET['page']) ? max((int)$_GET['page'], 1) : 1;
$offset = ($page - 1) * $limit;

$countStmt = $conn->prepare("SELECT COUNT(*) AS total FROM blogs b $whereSql");
if ($types) $countStmt->bind_param($types, ...$params);
$countStmt->execute();
$totalRows = $countStmt->get_result()->fetch_assoc()['total'];
$countStmt->close();
$totalPages = max(1, (int)ceil($totalRows / $limit));

$dataStmt = $conn->prepare("
    SELECT b.*, c.name AS category_name
    FROM blogs b
    LEFT JOIN blog_categories c ON b.category_id = c.id
    $whereSql
    ORDER BY b.id DESC
    LIMIT ? OFFSET ?
");
$allTypes = $types . 'ii';
$allParams = array_merge($params, [$limit, $offset]);
$dataStmt->bind_param($allTypes, ...$allParams);
$dataStmt->execute();
$result = $dataStmt->get_result();

$categories = mysqli_query($conn, "SELECT * FROM blog_categories ORDER BY name ASC");
$categoriesForAdd = mysqli_query($conn, "SELECT * FROM blog_categories ORDER BY name ASC");
$categoriesForEdit = mysqli_query($conn, "SELECT * FROM blog_categories ORDER BY name ASC");

$qsArray = array_filter([
    'search' => $search,
    'status' => $status !== null ? $status : '',
    'category' => $catId !== null ? $catId : '',
    'featured' => $featured !== null ? $featured : '',
], fn($v) => $v !== '');
$qs = http_build_query($qsArray);

include 'includes/header.php';
include 'includes/sidebar.php';

$reopenModal = $_SESSION['reopen_modal'] ?? '';
unset($_SESSION['reopen_modal']);
?>
<link rel="stylesheet" href="assets/css/admin-crud.css">

<div class="admin-content" data-reopen-modal="<?= htmlspecialchars($reopenModal) ?>">
  <h2>Blogs</h2>

  <?php include 'includes/admin-alert.php'; ?>

  <div class="crud-toolbar">
    <form class="toolbar-filters" method="GET">
      <div class="search-input">
        <input type="text" name="search" placeholder="Search title, author, tags..." value="<?= htmlspecialchars($search) ?>">
      </div>

      <select name="category" class="auto-submit">
        <option value="">All Categories</option>
        <?php while ($cat = mysqli_fetch_assoc($categories)): ?>
          <option value="<?= $cat['id'] ?>" <?= $catId === (int)$cat['id'] ? 'selected' : '' ?>><?= htmlspecialchars($cat['name']) ?></option>
        <?php endwhile; ?>
      </select>

      <select name="featured" class="auto-submit">
        <option value="">Featured?</option>
        <option value="1" <?= $featured === 1 ? 'selected' : '' ?>>Yes</option>
        <option value="0" <?= $featured === 0 ? 'selected' : '' ?>>No</option>
      </select>

      <select name="status" class="auto-submit">
        <option value="">All Status</option>
        <option value="1" <?= $status === 1 ? 'selected' : '' ?>>Published</option>
        <option value="0" <?= $status === 0 ? 'selected' : '' ?>>Draft</option>
      </select>

      <button type="submit" class="btn-filter"><i class="fa-solid fa-filter"></i> Filter</button>
      <?php if ($search !== '' || $status !== null || $catId !== null || $featured !== null): ?>
        <a href="manage-blogs" class="btn-reset">Reset</a>
      <?php endif; ?>
    </form>

    <button type="button" class="btn-add" onclick="openAddModal()"><i class="fa-solid fa-plus"></i> Add Blog</button>
  </div>

  <p class="result-count"><?= $totalRows ?> post<?= $totalRows == 1 ? '' : 's' ?> found</p>

  <div class="table-scroll"><table class="admin-table">
    <thead>
      <tr>
        <th>S.N.</th>
        <th>Date</th>
        <th>Cover</th>
        <th>Title</th>
        <th>Category</th>
        <th>Author</th>
        <th>Views</th>
        <th>Featured</th>
        <th>Status</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php $i = $offset + 1; ?>
      <?php if ($totalRows == 0): ?>
        <tr><td colspan="10" class="no-results">No blog posts match your search/filter.</td></tr>
      <?php endif; ?>
      <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
          <td><?= $i++ ?></td>
          <td><?= htmlspecialchars($row['created_at']) ?></td>
          <td><img src="uploads/images/blogs/<?= htmlspecialchars($row['cover_image']) ?>" height="50"></td>
          <td><?= htmlspecialchars($row['title']) ?></td>
          <td><?= htmlspecialchars($row['category_name'] ?? '-') ?></td>
          <td><?= htmlspecialchars($row['author']) ?></td>
          <td><?= (int)$row['views'] ?></td>
          <td><?= $row['is_featured'] ? '<span class="badge badge-popular">Yes</span>' : 'No'; ?></td>
          <td class="status-col">
            <a href="javascript:void(0)"
              onclick="showConfirm('toggle-blog?id=<?= $row['id'] ?>','<?= $row['status'] == 1 ? 'Unpublish' : 'Publish' ?> this post?')"
              class="pill <?= $row['status'] == 1 ? 'published' : 'draft' ?>">
              <?= $row['status'] == 1 ? 'Published' : 'Draft' ?>
            </a>
          </td>
          <td class="action-col">
            <a href="../blog-details?slug=<?= urlencode($row['slug']) ?>" target="_blank" class="btn-view">View</a>
            <button type="button" class="btn-edit"
              onclick="openEditModal(this)"
              data-id="<?= $row['id'] ?>"
              data-title="<?= htmlspecialchars($row['title']) ?>"
              data-category_id="<?= (int)$row['category_id'] ?>"
              data-author="<?= htmlspecialchars($row['author']) ?>"
              data-excerpt="<?= htmlspecialchars($row['excerpt']) ?>"
              data-content="<?= htmlspecialchars($row['content']) ?>"
              data-tags="<?= htmlspecialchars($row['tags']) ?>"
              data-meta_title="<?= htmlspecialchars($row['meta_title']) ?>"
              data-meta_description="<?= htmlspecialchars($row['meta_description']) ?>"
              data-meta_keywords="<?= htmlspecialchars($row['meta_keywords']) ?>"
              data-is_featured="<?= $row['is_featured'] ?>"
              data-status="<?= $row['status'] ?>"
              data-image="<?= htmlspecialchars($row['cover_image']) ?>"
              data-image-path="uploads/images/blogs/<?= htmlspecialchars($row['cover_image']) ?>">
              Edit
            </button>
            <a href="javascript:void(0)"
              onclick="showConfirm('?delete=<?= $row['id'] ?>&qs=<?= urlencode($qs) ?>','Delete this blog post?')"
              class="btn-delete">
              Delete
            </a>
          </td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table></div>

  <?php if ($totalPages > 1): ?>
    <div class="pagination">
      <?php for ($p = 1; $p <= $totalPages; $p++): ?>
        <a href="?<?= http_build_query(array_merge($qsArray, ['page' => $p])) ?>" class="page-btn <?= $p == $page ? 'active' : '' ?>"><?= $p ?></a>
      <?php endfor; ?>
    </div>
  <?php endif; ?>
</div>

<!-- ADD MODAL -->
<div class="crud-modal-overlay" id="addModal">
  <div class="crud-modal large">
    <button type="button" class="modal-close" onclick="closeModal('addModal')">&times;</button>
    <h2>Add New Blog</h2>
    <form method="POST" enctype="multipart/form-data" class="admin-form validate-form">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
      <input type="hidden" name="qs" value="<?= htmlspecialchars($qs) ?>">

      <div class="form-group"><input type="text" name="title" placeholder="Blog Title" data-validate="name" required><small class="error"></small></div>

      <label>Category</label>
      <select name="category_id">
        <option value="">-- Select Category --</option>
        <?php while ($cat = mysqli_fetch_assoc($categoriesForAdd)): ?>
          <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
        <?php endwhile; ?>
      </select>

      <div class="form-group"><input type="text" name="author" placeholder="Author (default: DTP Team)"></div>
      <div class="form-group"><textarea name="excerpt" placeholder="Short excerpt (leave blank to auto-generate)" maxlength="300"></textarea></div>
      <div class="form-group">
        <label>Content *</label>
        <textarea name="content" placeholder="Write your blog content here (HTML allowed)" data-validate="text20" required rows="10"></textarea>
        <small class="error"></small>
      </div>
      <div class="form-group"><input type="text" name="tags" placeholder="Tags, comma separated"></div>

      <div class="file_input">
        <label>Cover Image *</label>
        <input type="file" name="cover_image" accept="image/*" required>
      </div>

      <hr>
      <h3>SEO Settings</h3>
      <div class="form-group"><input type="text" name="meta_title" placeholder="Meta Title (max 70 chars)" maxlength="70"></div>
      <div class="form-group"><textarea name="meta_description" placeholder="Meta Description (max 160 chars)" maxlength="160"></textarea></div>
      <div class="form-group"><input type="text" name="meta_keywords" placeholder="Meta Keywords, comma separated"></div>

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
</div>

<!-- EDIT MODAL -->
<div class="crud-modal-overlay" id="editModal">
  <div class="crud-modal large">
    <button type="button" class="modal-close" onclick="closeModal('editModal')">&times;</button>
    <h2>Edit Blog</h2>
    <form method="POST" enctype="multipart/form-data" class="admin-form validate-form">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
      <input type="hidden" name="qs" value="<?= htmlspecialchars($qs) ?>">
      <input type="hidden" name="id" value="">

      <div class="form-group"><input type="text" name="title" placeholder="Blog Title" data-validate="name" required><small class="error"></small></div>

      <label>Category</label>
      <select name="category_id">
        <option value="">-- Select Category --</option>
        <?php while ($cat = mysqli_fetch_assoc($categoriesForEdit)): ?>
          <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
        <?php endwhile; ?>
      </select>

      <div class="form-group"><input type="text" name="author" placeholder="Author"></div>
      <div class="form-group"><textarea name="excerpt" placeholder="Short excerpt" maxlength="300"></textarea></div>
      <div class="form-group">
        <label>Content *</label>
        <textarea name="content" data-validate="text20" required rows="10"></textarea>
        <small class="error"></small>
      </div>
      <div class="form-group"><input type="text" name="tags" placeholder="Tags, comma separated"></div>

      <label>Current Cover</label>
      <div class="current-image">
        <img src="" width="120" alt="">
      </div>

      <div class="file_input">
        <label>Change Cover Image</label>
        <input type="file" name="cover_image" accept="image/*">
      </div>

      <hr>
      <h3>SEO Settings</h3>
      <div class="form-group"><input type="text" name="meta_title" maxlength="70" placeholder="Meta Title"></div>
      <div class="form-group"><textarea name="meta_description" maxlength="160" placeholder="Meta Description"></textarea></div>
      <div class="form-group"><input type="text" name="meta_keywords" placeholder="Meta Keywords"></div>

      <label>Featured?</label>
      <select name="is_featured">
        <option value="0">No</option>
        <option value="1">Yes</option>
      </select>

      <label>Status</label>
      <select name="status">
        <option value="1">Published</option>
        <option value="0">Draft</option>
      </select>

      <button name="update">Update Blog</button>
    </form>
  </div>
</div>

<script src="assets/js/form-validator.js"></script>
<script src="assets/js/admin-alert.js"></script>
<script src="assets/js/admin-crud.js"></script>

<?php include 'includes/footer.php'; ?>
<script src="../assets/js/confirmation.js"></script>
