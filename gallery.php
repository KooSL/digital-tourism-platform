<?php
$pageTitle = "Gallery";
include 'includes/header.php';
include 'config/db.php';
?>

<div class="header-wrapper">
  <?php include 'includes/topbar.php'; ?>
  <?php include 'includes/navbar.php'; ?>
</div>

<section class="page-banner">
  <div class="overlay">
    <h1>Gallery</h1>
    <p>Explore our travel memories</p>
  </div>
</section>

<section class="gallery-section">
  <div class="container">
    <div class="album-grid">

      <?php
      $albums = mysqli_query($conn, "SELECT * FROM gallery_albums WHERE status=1 ORDER BY created_at DESC");

      while ($album = mysqli_fetch_assoc($albums)) {
      ?>
        <div class="album-card">
          <a href="album?album=<?php echo $album['slug']; ?>">
            <img src="admin/uploads/gallery/<?php echo $album['slug']; ?>/<?php echo $album['cover_image']; ?>" alt="<?php echo $album['title']; ?>">
            <div class="album-overlay">
              <h3><?php echo $album['title']; ?></h3>
            </div>
          </a>
        </div>
      <?php } ?>

    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>