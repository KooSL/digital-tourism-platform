<aside class="admin-sidebar">
  <h2>TYS Admin</h2>

<?php if (isset($_SESSION['admin'])) { ?>
  <ul class="sidebar-menu">
    <li>
      <a href="dashboard">Dashboard</a>
    </li>

    <!-- TOURS DROPDOWN -->
    <li class="has-submenu">
      <a href="">Tours ▾</a>
      <ul class="submenu">
        <li><a href="add-tour">Add Tour</a></li>
        <li><a href="manage-tours">Manage Tours</a></li>
      </ul>
    </li>

    

    <li class="has-submenu">
      <a href="">Flights ▾</a>
      <ul class="submenu">
        <li><a href="add-flight-post">Add Flight</a></li>
        <li><a href="manage-flights">Manage Flights</a></li>
      </ul>
    </li>

    <li class="has-submenu">
      <a href="">Gallery ▾</a>
      <ul class="submenu">
        <li><a href="add-album">Add Album</a></li>
        <li><a href="add-photos">Add Photos</a></li>
        <li><a href="manage-albums">Manage</a></li>
      </ul>
    </li>

    <li>
      <a href="inquiries">Inquiries</a>
    </li>


    <li class="has-submenu">
      <a href="">Testimonials ▾</a>
      <ul class="submenu">
        <li><a href="add-testimonial">Add Testimonial</a></li>
        <li><a href="manage-testimonials">Manage Testimonials</a></li>
      </ul>
    </li>

    <li class="has-submenu">
      <a href="">Clients ▾</a>
      <ul class="submenu">
        <li><a href="add-client">Add Clients</a></li>
        <li><a href="manage-clients">Manage Clients</a></li>
      </ul>
    </li>

    <li class="has-submenu">
      <a href="">FAQs ▾</a>
      <ul class="submenu">
        <li><a href="add-faq">Add FAQ</a></li>
        <li><a href="manage-faqs">Manage FAQs</a></li>
      </ul>
    </li>

    <li>
      <a href="logout">Logout</a>
    </li>
  </ul>
  <?php } else { ?>
    <!-- <ul class="sidebar-menu">
    <li>
      <a href="dashboard.php">Dashboard</a>
    </li>

    <li class="has-submenu">
      <a href="">Tours ▾</a>
      <ul class="submenu">
        <li><a href="add-tour.php">Add Tour</a></li>
        <li><a href="manage-tours.php">Manage Tours</a></li>
      </ul>
    </li>

    <li>
      <a href="inquiries.php">Inquiries</a>
    </li>

  </ul> -->
  <?php } ?>
</aside>

