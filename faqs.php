<?php 
$pageTitle = "FAQs";
include 'includes/header.php'; ?>
<div class="header-wrapper">
  <?php include 'includes/topbar.php'; ?>
  <?php include 'includes/navbar.php'; ?>
</div>

<?php include 'config/db.php'; ?>

<section class="page-banner">
  <div class="overlay">
    <h1>Frequently Asked Questions</h1>
    <p>Everything you need to know before booking</p>
  </div>
</section>

<section class="faq-page">
  <div class="container">

    <div class="faq-list">
      <?php
      $faqs = mysqli_query(
        $conn,
        "SELECT * FROM faqs WHERE status = 1 ORDER BY id DESC"
      );

      while ($faq = mysqli_fetch_assoc($faqs)):
      ?>
        <div class="faq-item">
          <button class="faq-question">
            <?= htmlspecialchars($faq['question']) ?>
            <span><i class="fa-solid fa-angle-down"></i></span>
          </button>
          <div class="faq-answer">
            <p><?= nl2br(htmlspecialchars($faq['answer'])) ?></p>
          </div>
        </div>
      <?php endwhile; ?>
    </div>

  </div>
</section>

<script>
  document.querySelectorAll(".faq-question").forEach(btn => {
    btn.addEventListener("click", () => {
      btn.parentElement.classList.toggle("active");
    });
  });
</script>


<?php include 'includes/footer.php'; ?>
