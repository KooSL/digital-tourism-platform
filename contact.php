<?php
session_start();

if (empty($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

require_once 'config/db.php';
require_once 'includes/mailer.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['send'])) {

  if (
    !isset($_POST['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
  ) {
    die("CSRF validation failed.");
  }

  // Trim inputs
  $name    = trim($_POST['name'] ?? '');
  $email   = trim($_POST['email'] ?? '');
  $phone   = trim($_POST['phone'] ?? '');
  $message = trim($_POST['message'] ?? '');


  $stmt = $conn->prepare("INSERT INTO inquiries (name, email, phone, message) VALUES (?, ?, ?, ?)");

  if ($stmt) {
    $stmt->bind_param("ssss", $name, $email, $phone, $message);

    if ($stmt->execute()) {

      // FCM Notification
      require_once 'includes/fcm.php';
      sendFCMToAdmins(
        $conn,
        "New Contact Message",
        "New message received from $name"
      );

      // Email Notification
      $subject = "New Contact Message from $name";
      $body = "
                    <h3>New Contact Message Received</h3>
                    <p><strong>Name:</strong> " . htmlspecialchars($name) . "</p>
                    <p><strong>Email:</strong> " . htmlspecialchars($email) . "</p>
                    <p><strong>Phone:</strong> " . htmlspecialchars($phone) . "</p>
                    <p><strong>Message:</strong> " . nl2br(htmlspecialchars($message)) . "</p>
                ";

      sendAdminMail($subject, $body);

      header("Location: contact?success=1");
      exit;
    } else {
      die("Database execution failed.");
    }

    $stmt->close();
  } else {
    die("Database prepare failed.");
  }
}
?>


<?php include 'includes/header.php'; ?>
<div class="header-wrapper">
  <?php include 'includes/topbar.php'; ?>
  <?php include 'includes/navbar.php'; ?>
</div>


<!-- <section class="contact-hero">
  <div class="overlay">
    <div class="container">
      <h1>Contact Us</h1>
      <p>We’re here to help you plan your perfect journey</p>
    </div>
  </div>
</section> -->

<section class="page-banner">

  <?php if (isset($_GET['success'])): ?>
    <div class="success-box-contact" id="successBox">
      <strong>Success!</strong>
      <p>Your message has been sent successfully. We’ll contact you soon.</p>
    </div>
  <?php endif; ?>

  <div class="overlay">
    <h1>Contact Us</h1>
    <p>We are here to help you plan a seamless and unforgettable journey.</p>
  </div>
</section>

<section class="contact-section">
  <div class="container contact-flex">

    <div class="contact-left">
      <div class="contact-info">
        <h2>Get in Touch</h2>
        <p>
          Feel free to contact <strong>Take Your Seat</strong> for tour inquiries,
          bookings, or any travel-related questions.
        </p>

        <ul>
          <li><strong><i class="fa-solid fa-location-dot"></i> Address:</strong> Bharatpur-1, Chitwan, Nepal</li>
          <li><strong><i class="fa-solid fa-phone"></i> Telephone:</strong> 056-511777</li>
          <li><strong><i class="fa-solid fa-phone"></i> Phone / <i class="fa-brands fa-square-whatsapp"></i> Whatsapp:</strong> +977-9764667165</li>
          <li><strong><i class="fa-solid fa-envelope"></i> Email:</strong> contact.takeyourseat@gmail.com</li>
          <li><strong><i class="fa-solid fa-clock"></i> Working Hours:</strong> Sun–Fri | 10:00 AM – 6:00 PM</li>
        </ul>
      </div>




      <h2 class="map-title">Find Us on Map</h2>

      <div class="map-box">
        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d514.1725170795428!2d84.42609521821994!3d27.69352602815289!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3994fbc2ee459c9d%3A0x448dfca49a2caaa1!2sTake%20Your%20Seat%20Tours%20and%20Travels%20Pvt%20Ltd!5e0!3m2!1sen!2snp!4v1766342489448!5m2!1sen!2snp" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
      </div>

    </div>

    <div class="contact-left">
      <div class="contact-social">
        <h2>Follow Us</h2>
        <div class="social-icons">
          <a href="https://www.facebook.com/profile.php?id=61577350722166" target="_blank" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
          <a href="https://www.instagram.com/takeyourseat__tours/" target="_blank" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
          <a href="https://www.tiktok.com/@takeyourseat_tours" target="_blank" aria-label="YouTube"><i class="fa-brands fa-tiktok"></i></a>
          <a href="https://wa.me/+9779764667165" target="_blank" aria-label="WhatsApp"><i class="fa-brands fa-whatsapp"></i></a>
        </div>
      </div>

      <!-- CONTACT FORM -->
      <div class="contact-form-box">
        <h2>Send Us a Message</h2>

        <form method="POST" id="userForm" novalidate>

          <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

          <div class="form-group">
            <input type="text" name="name" id="name" placeholder="Full Name">
            <small class="error"></small>
          </div>

          <div class="form-group">
            <input type="email" name="email" id="email" placeholder="Email (Optional)">
            <small class="error"></small>
          </div>

          <div class="form-group">
            <input type="text" name="phone" id="phone" placeholder="Phone">
            <small class="error"></small>
          </div>

          <div class="form-group">
            <textarea name="message" id="message" placeholder="Your Message"></textarea>
            <small class="error"></small>
          </div>

          <button type="submit" name="send">Send</button>
        </form>

      </div>
    </div>

  </div>
</section>

<script src="assets/js/inq-cnt-validation.js"></script>
<script src="assets/js/success-errorBox.js"></script>

<?php include 'includes/footer.php'; ?>