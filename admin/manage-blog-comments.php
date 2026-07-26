<?php
include '../config/db.php';
include 'auth.php';

/* APPROVE / UNAPPROVE (update) */
if (isset($_GET['approve'])) {
    $id = (int)$_GET['approve'];
    mysqli_query($conn, "UPDATE blog_comments SET status = 1 WHERE id = $id");
    $_SESSION['success'] = "Comment approved.";
    header("Location: manage-blog-comments" . (isset($_GET['qs']) ? '?' . $_GET['qs'] : ''));
    exit;
}
if (isset($_GET['unapprove'])) {
    $id = (int)$_GET['unapprove'];
    mysqli_query($conn, "UPDATE blog_comments SET status = 0 WHERE id = $id");
    $_SESSION['success'] = "Comment hidden.";
    header("Location: manage-blog-comments" . (isset($_GET['qs']) ? '?' . $_GET['qs'] : ''));
    exit;
}

/* DELETE */
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM blog_comments WHERE id = $id");
    $_SESSION['success'] = "Comment deleted.";
    header("Location: manage-blog-comments" . (isset($_GET['qs']) ? '?' . $_GET['qs'] : ''));
    exit;
}

/* SEARCH + FILTER + PAGINATION */
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$status = isset($_GET['status']) && $_GET['status'] !== '' ? (int)$_GET['status'] : null;

$where = [];
$types = '';
$params = [];

if ($search !== '') {
    $where[] = "(bc.name LIKE ? OR bc.email LIKE ? OR bc.comment LIKE ?)";
    $like = "%$search%";
    $types .= 'sss';
    $params[] = $like; $params[] = $like; $params[] = $like;
}
if ($status !== null) {
    $where[] = "bc.status = ?";
    $types .= 'i';
    $params[] = $status;
}
$whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

$limit = 10;
$page = max((int)($_GET['page'] ?? 1), 1);
$offset = ($page - 1) * $limit;

$countStmt = $conn->prepare("SELECT COUNT(*) AS total FROM blog_comments bc $whereSql");
if ($types) $countStmt->bind_param($types, ...$params);
$countStmt->execute();
$totalRows = $countStmt->get_result()->fetch_assoc()['total'];
$countStmt->close();
$totalPages = max(1, (int)ceil($totalRows / $limit));

$dataStmt = $conn->prepare("
    SELECT bc.*, b.title AS blog_title, b.slug AS blog_slug
    FROM blog_comments bc
    LEFT JOIN blogs b ON bc.blog_id = b.id
    $whereSql
    ORDER BY bc.status ASC, bc.created_at DESC
    LIMIT ? OFFSET ?
");
$allTypes = $types . 'ii';
$allParams = array_merge($params, [$limit, $offset]);
$dataStmt->bind_param($allTypes, ...$allParams);
$dataStmt->execute();
$result = $dataStmt->get_result();

$qsArray = array_filter([
    'search' => $search,
    'status' => $status !== null ? $status : '',
], fn($v) => $v !== '');
$qs = http_build_query($qsArray);

include 'includes/header.php';
include 'includes/sidebar.php';
?>
<link rel="stylesheet" href="assets/css/admin-crud.css">

<div class="admin-content">
  <h2>Blog Comments</h2>

  <?php include 'includes/admin-alert.php'; ?>

  <div class="crud-toolbar">
    <form class="toolbar-filters" method="GET">
      <div class="search-input">
        <input type="text" name="search" placeholder="Search name, email, comment..." value="<?= htmlspecialchars($search) ?>">
      </div>
      <select name="status" class="auto-submit">
        <option value="">All Status</option>
        <option value="1" <?= $status === 1 ? 'selected' : '' ?>>Approved</option>
        <option value="0" <?= $status === 0 ? 'selected' : '' ?>>Pending</option>
      </select>
      <button type="submit" class="btn-filter"><i class="fa-solid fa-filter"></i> Filter</button>
      <?php if ($search !== '' || $status !== null): ?>
        <a href="manage-blog-comments" class="btn-reset">Reset</a>
      <?php endif; ?>
    </form>
  </div>

  <p class="result-count"><?= $totalRows ?> comment<?= $totalRows == 1 ? '' : 's' ?> found</p>

  <div class="table-scroll"><table class="admin-table">
    <thead>
      <tr>
        <th>S.N.</th>
        <th>Date</th>
        <th>Post</th>
        <th>Name</th>
        <th>Email</th>
        <th>Comment</th>
        <th>Status</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php $i = $offset + 1; ?>
      <?php if ($totalRows == 0): ?>
        <tr><td colspan="8" class="no-results">No comments match your search/filter.</td></tr>
      <?php endif; ?>
      <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
          <td><?= $i++ ?></td>
          <td><?= htmlspecialchars($row['created_at']) ?></td>
          <td>
            <a href="../blog-details?slug=<?= urlencode($row['blog_slug']) ?>#comments" target="_blank">
              <?= htmlspecialchars($row['blog_title'] ?? '(deleted post)') ?>
            </a>
          </td>
          <td><?= htmlspecialchars($row['name']) ?></td>
          <td><?= htmlspecialchars($row['email']) ?></td>
          <td><?= htmlspecialchars(mb_strimwidth($row['comment'], 0, 80, '...')) ?></td>
          <td class="status-col">
            <span class="pill <?= $row['status'] == 1 ? 'published' : 'draft' ?>"><?= $row['status'] == 1 ? 'Approved' : 'Pending' ?></span>
          </td>
          <td class="action-col">
            <?php if ($row['status'] == 1): ?>
              <a href="?unapprove=<?= $row['id'] ?>&qs=<?= urlencode($qs) ?>" class="btn-edit">Hide</a>
            <?php else: ?>
              <a href="?approve=<?= $row['id'] ?>&qs=<?= urlencode($qs) ?>" class="btn-edit">Approve</a>
            <?php endif; ?>
            <a href="javascript:void(0)"
              onclick="showConfirm('?delete=<?= $row['id'] ?>&qs=<?= urlencode($qs) ?>','Delete this comment?')"
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

<script src="assets/js/admin-alert.js"></script>
<script src="assets/js/admin-crud.js"></script>
<?php include 'includes/footer.php'; ?>
<script src="../assets/js/confirmation.js"></script>
