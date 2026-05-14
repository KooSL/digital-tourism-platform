<nav class="main-navbar">
  <div class="container nav-wrapper">

    <!-- LOGO -->
    <a href="/TakeYourSeat/index" class="logo">
      <!-- <img src="/TakeYourSeat/assets/images/TakeyourSeat_Logo-Color.png"> -->
    </a>

    <!-- MENU -->
    <ul class="nav-menu" id="navMenu">
      <li><a href="/TakeYourSeat">Home</a></li>
      <li class="dropdown">
        <a href="">Tours / Treks</a>
        <ul class="dropdown-menu">
          <li><a href="tours?type=domestic">Domestic</a></li>
          <li><a href="tours?type=international">International</a></li>
        </ul>
      </li>
      <li><a href="buses">Buses</a></li>
      <li><a href="flights">Flights</a></li>
      <li><a href="services">Services</a></li>
      <li><a href="gallery">Gallery</a></li>
      <li><a href="contact">Contact</a></li>
      <li><a href="about">About Us</a></li>
    </ul>

    <!-- HAMBURGER -->
    <div class="hamburger" id="hamburger">
      <span></span>
      <span></span>
      <span></span>
    </div>

    <!-- MOBILE MENU -->
    <div class="mobile-menu" id="mobileMenu">
      <ul>
        <li><a href="/TakeYourSeat">Home</a></li>
        <li class="mobile-dropdown">
          <a href="">Tours / Treks</a>
          <ul class="mobile-submenu">
            <li><a href="tours?type=domestic">Domestic</a></li>
            <li><a href="tours?type=international">International</a></li>
          </ul>
        </li>
        <li><a href="buses">Buses</a></li>
        <li><a href="flights">Flights</a></li>
        <li><a href="services">Services</a></li>
        <li><a href="gallery">Gallery</a></li>
        <li><a href="contact">Contact</a></li>
        <li><a href="about">About Us</a></li>
      </ul>
    </div>

  </div>
</nav>

<script>
  const hamburger = document.getElementById("hamburger");
  const mobileMenu = document.getElementById("mobileMenu");

  hamburger.addEventListener("click", () => {
    hamburger.classList.toggle("active");
    mobileMenu.classList.toggle("active");
  });

  document.querySelectorAll(".mobile-dropdown > a").forEach(item => {
  item.addEventListener("click", function(e) {
    e.preventDefault();
    this.parentElement.classList.toggle("active");
  });
});

</script>