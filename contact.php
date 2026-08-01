<?php
$pageTitle = "Contact";
include 'includes/header.php'; ?>

<?php

if (empty($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

require_once 'config/db.php';
require_once 'includes/mailer.php';
require_once 'includes/validation.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['send'])) {

  if (
    !isset($_POST['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
  ) {
    die("CSRF validation failed.");
  }

  // 5 messages per 10 minutes per session - a contact form with no login
  // wall is a common spam/abuse target.
  if (!checkRateLimit('contact_form', 5, 600)) {
    header("Location: contact?error=too_many_attempts");
    exit;
  }

  // Trim inputs
  $name    = trim($_POST['name'] ?? '');
  $email   = trim($_POST['email'] ?? '');
  $phone   = trim($_POST['phone'] ?? '');
  $message = trim($_POST['message'] ?? '');

  $v = new Validator();
  $v->honeypotEmpty('website', $_POST['website'] ?? '');
  $v->required('name', $name, 'Full name is required.')
    ->maxLength('name', $name, 100, 'Name is too long.');
  $v->email('email', $email, 'Please enter a valid email address.', false);
  $v->required('phone', $phone, 'Phone number is required.')
    ->phone('phone', $phone, 'Please enter a valid phone number.');
  $v->required('message', $message, 'Message cannot be empty.')
    ->minLength('message', $message, 10, 'Message is too short - please add a bit more detail.')
    ->maxLength('message', $message, 2000, 'Message is too long (max 2000 characters).');

  if ($v->fails()) {
    redirectWithErrors('contact', $v->errors(), [
      'name' => $name,
      'email' => $email,
      'phone' => $phone,
      'message' => $message,
    ]);
  }

  $stmt = $conn->prepare("INSERT INTO inquiries (name, email, phone, message) VALUES (?, ?, ?, ?)");

  if ($stmt) {
    $stmt->bind_param("ssss", $name, $email, $phone, $message);

    if ($stmt->execute()) {

      require_once __DIR__ . '/includes/send_fcm_notification.php';
      $customerName = $name;
      sendAdminNotification(
        '📩 New Contact Message Received!',
        $customerName . ' submitted a new contact message.',
        '/admin/inquiries.php'
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

      header("Location: contact?success=sent");
      exit;
    } else {
      header("Location: contact?error=failed");
      exit;
    }

    $stmt->close();
  } else {
    header("Location: contact?error=failed");
    exit;
  }
}
?>


<div class="header-wrapper">
  <?php include 'includes/topbar.php'; ?>
  <?php include 'includes/navbar.php'; ?>
</div>


<section class="page-banner">

  <?php if (isset($_GET['success'])): ?>
    <div class="success-box-contact" id="successBox">
      <strong>Success!</strong>
      <?php
      if ($_GET['success'] === 'sent') echo "Your message has been sent successfully. We’ll contact you soon.";
      ?>
    </div>
  <?php endif; ?>

  <?php if (isset($_GET['error']) && $_GET['error'] !== 'validation'): ?>
    <div class="error-box-contact" id="errorBox">
      <strong>Error!</strong>
      <?php
      if ($_GET['error'] === 'failed') echo "Message failed to send. Please try again.";
      if ($_GET['error'] === 'too_many_attempts') echo "Too many messages sent recently. Please try again in a few minutes.";
      ?>
    </div>
  <?php endif; ?>

  <?php if (($_GET['error'] ?? '') === 'validation') renderValidationErrors(); ?>

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
          Feel free to contact us for tour inquiries,
          bookings, or any travel-related questions.
        </p>

        <ul>
          <li><strong><i class="fa-solid fa-location-dot"></i> Address:</strong> Chitwan, Nepal</li>
          <li><strong><i class="fa-solid fa-phone"></i> Telephone:</strong> 056-123456</li>
          <li><strong><i class="fa-solid fa-phone"></i> Phone / <i class="fa-brands fa-square-whatsapp"></i> Whatsapp:</strong> +977-9812345678</li>
          <li><strong><i class="fa-solid fa-envelope"></i> Email:</strong> contact.dtp@gmail.com</li>
          <li><strong><i class="fa-solid fa-clock"></i> Working Hours:</strong> Sun–Fri | 10:00 AM – 5:00 PM</li>
        </ul>
      </div>

      <h2 class="map-title">Find Us on Map</h2>

      <div class="map-box">
        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d467.46101159637254!2d84.43384670509566!3d27.68345207834983!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3994fb2d933d355d%3A0x5e79bbe09d977ee3!2sSaptagandaki%20Multiple%20Campus!5e1!3m2!1sen!2snp!4v1778753106784!5m2!1sen!2snp" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
      </div>

    </div>

    <div class="contact-left">
      <div class="contact-social">
        <h2>Follow Us</h2>
        <div class="social-icons">
          <a href="https://www.facebook.com/" target="_blank" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
          <a href="https://www.instagram.com/" target="_blank" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
          <a href="https://www.tiktok.com/" target="_blank" aria-label="YouTube"><i class="fa-brands fa-tiktok"></i></a>
          <a href="https://wa.me/+9779812345678" target="_blank" aria-label="WhatsApp"><i class="fa-brands fa-whatsapp"></i></a>
        </div>
      </div>

      <!-- CONTACT FORM -->
      <div class="contact-form-box">
        <h2>Send Us a Message</h2>

        <form method="POST" id="userForm" novalidate>

          <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

          <!-- Honeypot field: hidden from real users via CSS (see below),
               left blank by humans, often auto-filled by simple spam bots. -->
          <div class="form-group hp-field" aria-hidden="true">
            <input type="text" name="website" tabindex="-1" autocomplete="off">
          </div>

          <div class="form-group">
            <input type="text" name="name" id="name" placeholder="Full Name" value="<?= oldInput('name') ?>">
            <small class="error"></small>
          </div>

          <div class="form-group">
            <input type="email" name="email" id="email" placeholder="Email (Optional)" value="<?= oldInput('email') ?>">
            <small class="error"></small>
          </div>

          <div class="form-group">
            <input type="text" name="phone" id="phone" placeholder="Phone" value="<?= oldInput('phone') ?>">
            <small class="error"></small>
          </div>

          <div class="form-group">
            <textarea name="message" id="message" placeholder="Your Message"><?= oldInput('message') ?></textarea>
            <small class="error"></small>
          </div>

          <button type="submit" name="send">Send</button>
        </form>

      </div>
    </div>

  </div>
</section>

<style>
  /* Honeypot field: exists in the DOM for bots to find, but invisible and
     unreachable for real users (display:none is skipped by some scrapers
     that check computed visibility, so this uses an off-screen position
     approach instead). */
  .hp-field {
    position: absolute !important;
    left: -9999px !important;
    top: -9999px !important;
    height: 0;
    overflow: hidden;
  }
</style>

<script src="assets/js/inq-cnt-validation.js"></script>
<script src="assets/js/success-errorBox.js"></script>

<?php clearOldInput(); ?>

<?php include 'includes/footer.php'; ?>