<?php
$pageTitle = "Album";
include 'includes/header.php';
include 'config/db.php';

if (!isset($_GET['album'])) {
  header("Location: gallery.php");
  exit;
}

$slug = $_GET['album'];

$stmt = mysqli_prepare($conn, "SELECT * FROM gallery_albums WHERE slug = ?");
mysqli_stmt_bind_param($stmt, "s", $slug);
mysqli_stmt_execute($stmt);
$album = mysqli_stmt_get_result($stmt);
$albumData = mysqli_fetch_assoc($album);

if (!$albumData) {
  header("Location: gallery.php");
  exit;
}

$stmt = mysqli_prepare($conn, "SELECT * FROM gallery_photos WHERE album_id = ?");
mysqli_stmt_bind_param($stmt, "i", $albumData['id']);
mysqli_stmt_execute($stmt);
$photos = mysqli_stmt_get_result($stmt);
?>

<div class="header-wrapper">
  <?php include 'includes/topbar.php'; ?>
  <?php include 'includes/navbar.php'; ?>
</div>

<section class="page-hero" style="background-image: url('uploads/gallery/<?php echo $slug; ?>/<?php echo $albumData['cover_image'] ?>');">
  <div class="overlay">
    <div class="container">
      <h1><?php echo $albumData['title']; ?></h1>
      <p>Album Photos</p>
    </div>
  </div>
</section>

<section class="gallery-section">
  <div class="container">
    <div class="photo-grid">

      <?php while ($photo = mysqli_fetch_assoc($photos)) { ?>
        <div class="photo-card">
          <img
            src="uploads/gallery/<?php echo $slug; ?>/<?php echo $photo['image']; ?>"
            alt=""
            class="gallery-img">

        </div>
      <?php } ?>

    </div>
  </div>
</section>

<div id="lightbox" class="lightbox">

  <div class="lightbox-top">
    <a id="downloadBtn" class="img-download-btn" download>
      <i class="fa-solid fa-arrow-down fa-xs"></i>
    </a>

    <span class="close">
      <i class="fa-solid fa-xmark fa-xs"></i>
    </span>
  </div>

  <button class="nav prev">
    <i class="fa-solid fa-chevron-left fa-xs"></i>
  </button>

  <img class="lightbox-img" id="lightboxImg">

  <button class="nav next">
    <i class="fa-solid fa-chevron-right fa-xs"></i>
  </button>

</div>



<script src="assets/js/album-lightbox.js"></script>

<?php include 'includes/footer.php'; ?>