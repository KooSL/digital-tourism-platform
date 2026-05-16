<?php
if (isset($_GET['i'])) {
  $url = strtok($_SERVER["REQUEST_URI"], '?');
  header("Location: $url", true, 301);
  exit;
}
?>


<?php include 'includes/header.php'; ?>

<!-- HEADER -->
<div class="header-wrapper">
  <?php include 'includes/topbar.php'; ?>
  <?php include 'includes/navbar.php'; ?>
</div>

<?php include 'config/db.php'; ?>

<!-- HERO SECTION -->
<section class="home-hero" style="background-image: url('assets/images/home_page/home_page_6.jpg');">
  <div class="hero-overlay">
    <div class="container-hero-content">

      <!-- <img src="assets/images/logo/airplane-icon_white.png" class="airplane-icon" alt="Airplane"> -->

      <div class="hero-search">
        <form>
          <input type="text" id="tourSearch" placeholder="Search tours">
          <button type="submit" disabled>
            <i class="fa-solid fa-magnifying-glass"></i>
          </button>
        </form>
        <div id="searchResults" class="search-results"></div>
      </div>

      <p class="hero-lgt">Let's go together</p>

      <!-- <div class="view-btn">
        <a href="tours.php" class="btn-primary-vtp">View Tour Packages</a>
      </div> -->

    </div>
  </div>
</section>


<!-- POPULAR TOURS -->
<section class="home-tours">
  <div class="container">

    <h2 class="section-title">Explore Our Latest and Popular Packages</h2>
    <p class="section-subtitle-explore">
      Digital Tourism Platform offers carefully designed packages focusing on comfort,
      safety, and unforgettable travel experiences.
    </p>

    <!-- DOMESTIC TOURS -->
    <h2 class="section-title type">Our Domestic Packages</h2>

    <div class="tour-slider-wrapper">

      <button class="slider-btn prev domestic-prev">
        <i class="fas fa-chevron-left"></i>
      </button>

      <div class="tour-slider-viewport">
        <div class="tour-slider-track domestic-track">

          <?php
          $domestic = mysqli_query(
            $conn,
            "SELECT * FROM tours 
             WHERE status = 1 AND type = 'domestic' 
             ORDER BY is_popular DESC, created_at DESC"
          );

          while ($tour = mysqli_fetch_assoc($domestic)) {
          ?>
            <div class="tour-card">
              <div class="tour-card-img">
                <?php if ($tour['is_popular'] == 1): ?>
                  <span class="popular-badge-home">
                    <i class="fa-solid fa-fire"></i> Popular
                  </span>
                <?php endif; ?>

                <?php if (strtotime($tour['created_at']) >= strtotime('-7 days')): ?>
                  <span class="latest-badge-home">
                    <i class="fa-solid fa-star"></i> Latest
                  </span>
                <?php endif; ?>

                <img
                  src="admin/uploads/images/tours/<?= $tour['banner_image']; ?>"
                  alt="<?= htmlspecialchars($tour['title']); ?>">
              </div>

              <div class="tour-info">
                <h3><?= htmlspecialchars($tour['title']); ?></h3>
                <p>NPR <?= htmlspecialchars($tour['price']); ?> | USD $<?= htmlspecialchars($tour['price_usd']); ?></p>
              </div>

              <div class="tour-card-btn">
                <a href="tour-details?id=<?= $tour['id']; ?>">View Details</a>
              </div>
            </div>
          <?php } ?>

        </div>
      </div>

      <button class="slider-btn next domestic-next">
        <i class="fas fa-chevron-right"></i>
      </button>

    </div>


    <!-- INTERNATIONAL TOURS -->
    <h2 class="section-title type">Our International Packages</h2>

    <div class="tour-slider-wrapper">

      <button class="slider-btn prev international-prev">
        <i class="fas fa-chevron-left"></i>
      </button>

      <div class="tour-slider-viewport">
        <div class="tour-slider-track international-track">

          <?php
          $international = mysqli_query(
            $conn,
            "SELECT * FROM tours 
             WHERE status = 1 AND type = 'international' 
             ORDER BY is_popular DESC, created_at DESC"
          );

          while ($tour = mysqli_fetch_assoc($international)) {
          ?>
            <div class="tour-card">
              <div class="tour-card-img">
                <?php if ($tour['is_popular'] == 1): ?>
                  <span class="popular-badge-home">
                    <i class="fa-solid fa-fire"></i> Popular
                  </span>
                <?php endif; ?>

                <?php if (strtotime($tour['created_at']) >= strtotime('-7 days')): ?>
                  <span class="latest-badge-home">
                    <i class="fa-solid fa-star"></i> Latest
                  </span>
                <?php endif; ?>

                <img
                  src="admin/uploads/images/tours/<?= $tour['banner_image']; ?>"
                  alt="<?= htmlspecialchars($tour['title']); ?>">
              </div>

              <div class="tour-info">
                <h3><?= htmlspecialchars($tour['title']); ?></h3>
                <p>NPR <?= htmlspecialchars($tour['price']); ?> | USD $<?= htmlspecialchars($tour['price_usd']); ?></p>
              </div>

              <div class="tour-card-btn">
                <a href="tour-details?id=<?= $tour['id']; ?>">View Details</a>
              </div>
            </div>
          <?php } ?>

        </div>
      </div>

      <button class="slider-btn next international-next">
        <i class="fas fa-chevron-right"></i>
      </button>

    </div>

    <div class="center-btn">
      <a href="tours" class="btn-primary-em">Explore More</a>
    </div>

  </div>
</section>


<!-- INTERNATIONAL FLIGHTS -->
<section class="home-flights">
  <div class="container">
    <h2 class="section-title">International Flight Booking</h2>
    <p class="section-subtitle-flight">
      Best airfare deals with professional ticketing assistance
    </p>



    <div class="flight-slider-wrapper">

      <!-- PREV BUTTON -->
      <button class="slider-btn prev" id="flightPrevBtn">
        <i class="fas fa-chevron-left"></i>
      </button>

      <!-- VIEWPORT -->
      <div class="flight-slider-viewport">
        <div class="flight-slider-track" id="flightTrack">

          <?php
          $flights = mysqli_query(
            $conn,
            "SELECT * FROM flights WHERE status = 1 ORDER BY id DESC LIMIT 3"
          );

          while ($flight = mysqli_fetch_assoc($flights)) {
          ?>
            <div class="flight-card">
              <div class="flight-card-img">

                <?php if ($flight['is_group_fare'] == 1): ?>
                  <span class="group-fare-badge">
                    <i class="fa-solid fa-users"></i> Group Fare
                  </span>
                <?php endif; ?>

                <img src="admin/uploads/images/flights/<?= $flight['image']; ?>"
                  alt="<?= $flight['from_city'] . ' to ' . $flight['to_city']; ?>">

              </div>

              <div class="flight-info">
                <h3>
                  <?= htmlspecialchars($flight['from_city']); ?> →
                  <?= htmlspecialchars($flight['to_city']); ?>
                </h3>

                <!-- Optional Description -->
                <!-- <p><?= htmlspecialchars($flight['description']); ?></p> -->

                <!-- Price -->
                <!-- <?php if ($flight['price']) { ?>
              <p><strong>From:</strong> <?= $flight['price']; ?></p>
            <?php } ?> -->

                <!-- Details Page -->
                <a href="flight-details?id=<?= $flight['id']; ?>"
                  class="btn-primary">
                  View Details
                </a>
              </div>
            </div>
          <?php } ?>

        </div>
      </div>

      <!-- NEXT BUTTON -->
      <button class="slider-btn next" id="flightNextBtn">
        <i class="fas fa-chevron-right"></i>
      </button>

    </div>

    <a href="flights" class="btn-primary-flight">
      More
    </a>
  </div>
</section>

<!-- SERVICES -->
<section class="services">
  <div class="container">

    <h2 class="section-title">Our Services</h2>
    <p class="section-subtitle">Complete travel services under one roof</p>

    <div class="services-grid">



      <div class="service-card">
        <span><i class="fa-solid fa-plane-departure" style="color: #006db6;"></i></span>
        <h3>Flight Ticketing</h3>
        <p>International & domestic flight booking assistance.</p>
      </div>

      <div class="service-card">
        <span><i class="fa-solid fa-earth-americas" style="color: #006db6;"></i></span>
        <h3>Tour Packages</h3>
        <p>Domestic & international tour packages tailored for you.</p>
      </div>

      <div class="service-card">
        <span><i class="fa-solid fa-person-hiking" style="color: #006db6;"></i></span>
        <h3>Trek Packages</h3>
        <p>Everest, Annapurna & many more trekking adventures.</p>
      </div>

      <div class="service-card">
        <span><i class="fa-solid fa-passport" style="color: #006db6;"></i> </span>
        <h3>Visa Services</h3>
        <p>Professional visa consultation & documentation support.</p>
      </div>

      <div class="service-card">
        <span><i class="fa-solid fa-bus-side" style="color: #006db6;"></i></span>
        <h3>Bus Ticketing</h3>
        <p>Easy bus ticketing for domestic & international destinations.</p>
      </div>

      <div class="service-card">
        <span><i class="fa-solid fa-house-fire" style="color: #006db6;"></i></span>
        <h3>Camping & Equipments</h3>
        <p>Camping trips and rental of quality camping gear.</p>
      </div>

      <div class="service-card">
        <span><i class="fa-solid fa-person-skiing-nordic" style="color: #006db6;"></i></span>
        <h3>Adventure Activities</h3>
        <p>Bungee Jumping, Rafting, paragliding & more.</p>
      </div>

      <div class="service-card">
        <span><i class="fa-solid fa-paw" style="color: #006db6;"></i></span>
        <h3>Wildlife Activities</h3>
        <p>Jungle safari, wildlife tours & nature experiences.</p>
      </div>


    </div>
  </div>
</section>

<!-- CLIENT STATISTICS -->
<section class="stats-section">
  <div class="container">

    <h2 class="section-title">Our Achievements</h2>
    <p class="section-subtitle">Numbers that reflect our journey</p>

    <div class="stats-grid">

      <div class="stat-box">
        <h3 class="counter" data-target="4200">0</h3>
        <p>Happy Travelers</p>
      </div>

      <div class="stat-box">
        <h3 class="counter" data-target="1400">0</h3>
        <p>Flight Bookings</p>
      </div>

      <div class="stat-box">
        <h3 class="counter" data-target="1250">0</h3>
        <p>Tour Packages</p>
      </div>

      <div class="stat-box">
        <h3 class="counter" data-target="25">0</h3>
        <p>Visa Applications Processed</p>
      </div>

      <div class="stat-box">
        <h3 class="counter" data-target="700">0</h3>
        <p>Bus Ticket Bookings</p>
      </div>

    </div>
  </div>
</section>

<!-- AIRLINE PARTNERS -->
<section class="airline-partners">
  <h2 class="section-title">Our Airline Partners</h2>
  <p class="section-subtitle">Global airline partnerships for seamless travel</p>

  <div class="marquee">
    <div class="marquee-track">
      <!-- Repeat logos twice for seamless loop -->
      <img src="assets/images/airlines/qatar.png" alt="">
      <img src="assets/images/airlines/emirates.png" alt="">
      <img src="assets/images/airlines/nepalairlines.png" alt="">
      <img src="assets/images/airlines/flydubai.png" alt="">
      <img src="assets/images/airlines/airindia.png" alt="">
      <img src="assets/images/airlines/turkish.png" alt="">
      <img src="assets/images/airlines/airasia.png" alt="">
      <img src="assets/images/airlines/indigo.png" alt="">
      <img src="assets/images/airlines/thaiairways.png" alt="">


      <!-- duplicate -->
      <img src="assets/images/airlines/qatar.png" alt="">
      <img src="assets/images/airlines/emirates.png" alt="">
      <img src="assets/images/airlines/nepalairlines.png" alt="">
      <img src="assets/images/airlines/flydubai.png" alt="">
      <img src="assets/images/airlines/airindia.png" alt="">
      <img src="assets/images/airlines/turkish.png" alt="">
      <img src="assets/images/airlines/airasia.png" alt="">
      <img src="assets/images/airlines/indigo.png" alt="">
      <img src="assets/images/airlines/thaiairways.png" alt="">
    </div>
  </div>
</section>


<!-- TESTIMONIALS -->
<section class="testimonials">
  <div class="container">

    <h2 class="section-title">What Our Clients Say</h2>
    <p class="section-subtitle-testimonial">
      Real experiences from travelers who trusted Digital Tourism Platform
    </p>

    <div class="testimonial-slider-wrapper">

      <button class="slider-btn prev" onclick="slideTestimonial(-1)" id="testimonialprevBtn">
        <i class="fas fa-chevron-left"></i>
      </button>

      <div class="testimonial-viewport">
        <div class="testimonial-track" id="testimonialTrack">
          <?php
          $testimonials = mysqli_query(
            $conn,
            "SELECT * FROM testimonials WHERE status = 1 ORDER BY id DESC"
          );
          ?>

          <?php while ($t = mysqli_fetch_assoc($testimonials)): ?>

            <div class="testimonial-card">
              <div class="stars">
                <?= str_repeat('★', $t['rating']); ?>
              </div>

              <p class="review">
                <?= htmlspecialchars($t['review']); ?>
              </p>

              <h4><?= htmlspecialchars($t['name']); ?></h4>

              <span><?= htmlspecialchars($t['service']); ?></span>
            </div>

          <?php endwhile; ?>

        </div>
      </div>

      <button class="slider-btn next" onclick="slideTestimonial(1)" id="testimonialnextBtn">
        <i class="fas fa-chevron-right"></i>
      </button>

    </div>

  </div>
</section>

<?php
$faqs = mysqli_query(
  $conn,
  "SELECT * FROM faqs
   WHERE status = 1 AND is_featured = 1
   ORDER BY id DESC
   LIMIT 4"
);
?>

<section class="home-faq">
  <div class="container">

    <h2 class="section-title">Frequently Asked Questions</h2>
    <p class="section-subtitle">
      Quick answers to common travel questions
    </p>

    <div class="faq-list">
      <?php while ($faq = mysqli_fetch_assoc($faqs)): ?>
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

    <div class="center-btn">
      <a href="faqs" class="btn-primary-faq">View All FAQs</a>
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


<!-- WHY CHOOSE US -->
<section class="why-us">
  <div class="container">

    <h2 class="section-title white">Why Choose Digital Tourism Platform?</h2>

    <div class="why-grid">

      <div class="why-box">
        <span class="why-icon"><i class="fa-solid fa-earth-americas" style="color: #006db6;"></i></span>
        <h3>Local Expertise</h3>
        <p>Experienced team with deep local knowledge.</p>
      </div>

      <div class="why-box">
        <span class="why-icon"><i class="fa-solid fa-hand-holding-dollar" style="color: #006db6;"></i></span>
        <h3>Affordable Pricing</h3>
        <p>Best value packages without compromising quality.</p>
      </div>

      <div class="why-box">
        <span class="why-icon"><i class="fa-solid fa-star" style="color: #006db6;"></i></span>
        <h3>Trusted Service</h3>
        <p>Customer satisfaction and safety first.</p>
      </div>

      <div class="why-box">
        <span class="why-icon"><i class="fa-solid fa-phone" style="color: #006db6;"></i></span>
        <h3>24/7 Support</h3>
        <p>We are always available for our travelers.</p>
      </div>

    </div>
  </div>
</section>

<!-- DESTINATIONS -->
<!-- <section class="destinations">
  <div class="container">

    <h2 class="section-title">Top Destinations</h2>

    <div class="destination-grid">
      <div class="destination-card">Nepal</div>
      <div class="destination-card">India</div>
      <div class="destination-card">Dubai</div>
      <div class="destination-card">Thailand</div>
    </div>

  </div>
</section> -->

<!-- HAPPY CLIENTS -->
<?php
$clients = mysqli_query(
  $conn,
  "SELECT * FROM clients WHERE status = 1 ORDER BY id DESC"
);
?>

<section class="happy-clients">
  <h2 class="section-title">Our Happy Clients</h2>
  <p class="section-subtitle">Trusted by clients across Nepal and beyond</p>

  <div class="client-marquee">
    <div class="client-marquee-track">

      <?php while ($client = mysqli_fetch_assoc($clients)): ?>
        <img
          src="admin/uploads/images/clients/<?= $client['logo'] ?>"
          alt="<?= htmlspecialchars($client['name']) ?>">
      <?php endwhile; ?>

      <!-- duplicate for smooth loop -->
      <?php
      mysqli_data_seek($clients, 0);
      while ($client = mysqli_fetch_assoc($clients)):
      ?>
        <img
          src="admin/uploads/images/clients/<?= $client['logo'] ?>"
          alt="<?= htmlspecialchars($client['name']) ?>">
      <?php endwhile; ?>

    </div>
  </div>
</section>


<!-- CTA -->
<section class="home-cta">
  <div class="cta-overlay">
    <div class="container">
      <h2>Ready to Plan Your Trip?</h2>
      <p>Let us help you create unforgettable travel experiences.</p>
      <a href="contact" class="btn-primary">Contact Us Now</a>
    </div>
  </div>
</section>


<script src="assets/js/search-tours.js"></script>
<script src="assets/js/tour-slider.js"></script>
<script src="assets/js/flight-slider.js"></script>
<script src="assets/js/statistics.js"></script>
<script src="assets/js/airlines-slider.js"></script>
<script src="assets/js/testimonial-slider.js"></script>
<!-- <script src="assets/js/testimonial-btns.js"></script> -->
<script src="assets/js/clients-slider.js"></script>



<?php include 'includes/footer.php'; ?>