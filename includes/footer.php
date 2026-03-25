<footer class="site-footer">
  <div class="container footer-grid">

    <!-- ABOUT -->
    <div class="footer-box">
      <div class="footer-box-logo">
        <a href="/TakeYourSeat" class="logo">
          <img src="assets/images/logo/Take your Seat Logo- White.png" alt="Take Your Seat Logo">
        </a>
      </div>
      <!-- <p class="footer-lgt">"LET'S GO TOGETHER"</p> -->
    </div>

    <!-- QUICK LINKS -->
    <div class="footer-box">
      <h3>Quick Links</h3>
      <ul>
        <!-- <li><a href="index.php">Home</a></li> -->
        <li><a href="tours">Tour Packages</a></li>
        <li><a href="services">Our Services</a></li>
        <li><a href="gallery">Gallery</a></li>
        <li><a href="contact">Contact Us</a></li>
        <li><a href="about">About Us</a></li>
      </ul>
    </div>

    <!-- TOURS -->
    <div class="footer-box">
      <h3>Popular Tours</h3>
      <ul>
        <?php
        $tourQuery = mysqli_query(
          $conn,
          "SELECT id, title 
            FROM tours 
            WHERE status = 1 AND is_popular = 1 
            ORDER BY id DESC 
            LIMIT 5"
        );

        while ($tour = mysqli_fetch_assoc($tourQuery)) {
        ?>
          <li>
            <a href="tour-details?id=<?= $tour['id']; ?>">
              <?= htmlspecialchars($tour['title']); ?>
            </a>
          </li>
        <?php } ?>
      </ul>
    </div>

    <div class="footer-box">
      <h3>Useful Links</h3>
      <ul>
        <li><a href="t&c">Terms & Conditions</a></li>
        <li><a href="privacy-policy">Privacy Policy</a></li>
      </ul>
    </div>


    <!-- CONTACT -->
    <div class="footer-box">
      <h3>Contact Info</h3>
      <div class="footer-contacts">
        <p><i class="fa-solid fa-location-dot"></i> Bharatpur-1, Chitwan, Nepal</p>
        <p><i class="fa-solid fa-phone"></i> / <i class="fa-brands fa-whatsapp"></i> +977-9764667165</p>
        <p><i class="fa-solid fa-envelope"></i>contact.takeyourseat@gmail.com</p>
      </div>
      <!-- SOCIAL ICONS -->
      <div class="footer-social">
        <a href="https://www.facebook.com/profile.php?id=61577350722166" target="_blank" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
        <a href="https://www.instagram.com/takeyourseat__tours/" target="_blank"aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
        <a href="https://www.tiktok.com/@takeyourseat_tours" target="_blank" aria-label="YouTube"><i class="fa-brands fa-tiktok"></i></a>
        <a href="https://wa.me/+9779764667165" target="_blank" aria-label="WhatsApp"><i class="fa-brands fa-whatsapp"></i></a>
      </div>
    </div>
  </div>

  <!-- FOOTER EXTRA INFO -->
  <div class="footer-extra">

    <!-- PAYMENT METHODS -->
    <div class="footer-extra-box">
      <h3>We Accept</h3>
      <div class="footer-logos">
        <a href="https://esewa.com.np/"><img src="assets/images/payments/esewa_2.png" alt="eSewa"></a>
        <a href="https://khalti.com/"><img src="assets/images/payments/khalti.png" alt="Khalti"></a>
        <a href="#"><img src="assets/images/payments/mobile-banking.jpg" alt="Mobile Banking"></a>
        <!-- <a href="#"><img src="assets/images/payments/cash.jpg" alt="Cash"></a> -->
      </div>
    </div>

    <!-- ASSOCIATED WITH -->
    <div class="footer-extra-box">
      <h3>Associated With</h3>
      <div class="footer-logos">
        <!-- <a href="https://www.iata.org/"><img src="assets/images/assoc_partners/iata.jpg" alt="IATA"></a> -->
        <a href="https://ntb.gov.np/"><img src="assets/images/assoc_partners/nepal_tourism.jpg" alt="Nepal Tourism Board"></a>
        <a href="https://opmcm.gov.np/"><img src="assets/images/assoc_partners/nepal_gov.svg" alt="nepal_gov"></a>
      </div>
    </div>

    <!-- Connect With us -->
    <!-- <div class="footer-extra-box">
      <h3>Connect With us</h3>
      <div class="footer-social">
        <a href="https://www.facebook.com/profile.php?id=61577350722166" target="_blank" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
        <a href="https://www.instagram.com/takeyourseat__tours/" target="_blank"aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
        <a href="https://www.tiktok.com/@takeyourseat_tours" target="_blank" aria-label="YouTube"><i class="fa-brands fa-tiktok"></i></a>
        <a href="https://wa.me/9779865507624" target="_blank" aria-label="WhatsApp"><i class="fa-brands fa-whatsapp"></i></a>
      </div>
    </div> -->

  </div>


  <!-- COPYRIGHT -->
  <div class="footer-bottom">
    <p>© <?php echo date("Y"); ?> Take Your Seat. All Rights Reserved.</p>
  </div>
</footer>


</body>
</html>
  