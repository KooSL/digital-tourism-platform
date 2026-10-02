<footer class="site-footer">
  <div class="container footer-grid">

    <!-- ABOUT -->
    <div class="footer-box">
      <div class="footer-box-logo">
        <a href="/Digital_Tourism_Platform" class="logo">
          <!-- <img src="assets/images/logo/Take your Seat Logo- White.png" alt="Take Your Seat Logo"> -->
          DTP
        </a>
      </div>
      <!-- <p class="footer-lgt">"LET'S GO TOGETHER"</p> -->
    </div>

    <!-- QUICK LINKS -->
    <div class="footer-box">
      <h3>Quick Links</h3>
      <ul>
        <!-- <li><a href="index.php">Home</a></li> -->
        <li><a href="trips">Trip Packages</a></li>
        <li><a href="services">Our Services</a></li>
        <li><a href="gallery">Gallery</a></li>
        <li><a href="contact">Contact Us</a></li>
        <li><a href="about">About Us</a></li>
      </ul>
    </div>

    <!-- trips -->
    <div class="footer-box">
      <h3>Popular Trips</h3>
      <ul>
        <?php
        $tripQuery = mysqli_query(
          $conn,
          "SELECT id, slug, title 
            FROM trips 
            WHERE status = 1 AND is_popular = 1 
            ORDER BY id DESC 
            LIMIT 5"
        );

        while ($trip = mysqli_fetch_assoc($tripQuery)) {
        ?>
          <li>
            <a href="trip-details?trip=<?= $trip['slug']; ?>">
              <?= htmlspecialchars($trip['title']); ?>
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
        <p><i class="fa-solid fa-location-dot"></i> Chitwan, Nepal</p>
        <p><i class="fa-solid fa-phone"></i> / <i class="fa-brands fa-whatsapp"></i> +977-9812345678</p>
        <p><i class="fa-solid fa-envelope"></i> contact.dtp@gmail.com</p>
      </div>
      <!-- SOCIAL ICONS -->
      <div class="footer-social">
        <a href="https://www.facebook.com/" target="_blank" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
        <a href="https://www.instagram.com/" target="_blank" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
        <a href="https://www.tiktok.com/" target="_blank" aria-label="YouTube"><i class="fa-brands fa-tiktok"></i></a>
        <a href="https://wa.me/+9779812345678" target="_blank" aria-label="WhatsApp"><i class="fa-brands fa-whatsapp"></i></a>
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
        <a href="https://www.instagram.com/takeyourseat__trips/" target="_blank"aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
        <a href="https://www.tiktok.com/@takeyourseat_trips" target="_blank" aria-label="YouTube"><i class="fa-brands fa-tiktok"></i></a>
        <a href="https://wa.me/9779865507624" target="_blank" aria-label="WhatsApp"><i class="fa-brands fa-whatsapp"></i></a>
      </div>
    </div> -->

  </div>


  <!-- COPYRIGHT -->
  <div class="footer-bottom">
    <p>© <?php echo date("Y"); ?> Digital Tourism Platform. All Rights Reserved.</p>
  </div>
</footer>

<div id="confirmModal" class="confirm-overlay">

  <div class="confirm-box">

    <h3 id="confirmTitle">Are you sure?</h3>

    <p id="confirmMessage">
      This action cannot be undone.
    </p>

    <div class="confirm-actions">

      <button id="cancelBtn" class="cancel">
        Cancel
      </button>

      <a id="confirmBtn" href="#" class="confirm">
        Confirm
      </a>

    </div>
  </div>
</div>

<div id="chatContainer">
  <div id="chatHeader">
    <div>
      <strong>Guide Dai</strong><br>
      <small>AI Assistant</small>
    </div>
    <span id="closeChat"><i class="fa-solid fa-xmark"></i></span>
  </div>

  <div id="chatMessages"></div>

  <div id="chatInputArea">
    <input type="text" id="userInput" placeholder="Ask about trips, buses..." />
    <button onclick="sendMessage()"><i class="fa-solid fa-paper-plane"></i></button>
  </div>

  <div class="chatFooter">
    <p>Guide Dai is a AI Assistant and can make mistakes. Please check responses carefully.</p>
  </div>

</div>

<div class="chatbotAndbackToTopGrid">
  <div id="chatToggle"><img src="assets/images/chatbot/icon2.png" alt=""></div>
  <div class="backToTop">
    <button id="backToTop">↑</button>
  </div>
</div>
<script src="assets/js/cbassistant.js"></script>
<script src="assets/js/backToTop.js"></script>

</body>

</html>