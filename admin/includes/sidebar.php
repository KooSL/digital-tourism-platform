<aside class="admin-sidebar">
  <!-- <h2><span class="brand-mark"></span><span class="link-text">DTP Admin</span></h2> -->
  <h2><span class="link-text">DTP Admin</span></h2>

  <?php if (isset($_SESSION['admin'])) { ?>
    <ul class="sidebar-menu">
      <li>
        <a href="dashboard"><i class="fa-solid fa-gauge-high"></i><span class="link-text">Dashboard</span></a>
      </li>

      <li>
        <a href="manage-tours"><i class="fa-solid fa-map-location-dot"></i><span class="link-text">Tours</span></a>
      </li>

      <li class="has-submenu">
        <a href="javascript:void(0)"><i class="fa-solid fa-bus"></i><span class="link-text">Buses</span></a>
        <ul class="submenu">
          <li><a href="manage-buses">Buses</a></li>
          <li><a href="bus-inquiries">Bus Inquiries</a></li>
        </ul>
      </li>

      <li>
        <a href="manage-flights"><i class="fa-solid fa-plane"></i><span class="link-text">Flights</span></a>
      </li>

      <li class="has-submenu">
        <a href="javascript:void(0)"><i class="fa-solid fa-ticket"></i><span class="link-text">Bookings</span></a>
        <ul class="submenu">
          <li><a href="package-bookings">Package Bookings</a></li>
        </ul>
      </li>

      <li>
        <a href="inquiries"><i class="fa-solid fa-comments"></i><span class="link-text">Inquiries</span></a>
      </li>

      <li>
        <a href="users"><i class="fa-solid fa-users"></i><span class="link-text">Users</span></a>
      </li>

      <li>
        <a href="manage-albums"><i class="fa-solid fa-images"></i><span class="link-text">Gallery</span></a>
      </li>

      <li class="has-submenu">
        <a href="javascript:void(0)"><i class="fa-solid fa-newspaper"></i><span class="link-text">Blogs</span></a>
        <ul class="submenu">
          <li><a href="manage-blogs">Blogs</a></li>
          <li><a href="manage-blog-categories">Categories</a></li>
          <li><a href="manage-blog-comments">Comments</a></li>
        </ul>
      </li>

      <li>
        <a href="manage-testimonials"><i class="fa-solid fa-star"></i><span class="link-text">Testimonials</span></a>
      </li>

      <li>
        <a href="manage-clients"><i class="fa-solid fa-handshake"></i><span class="link-text">Clients</span></a>
      </li>

      <li>
        <a href="manage-faqs"><i class="fa-solid fa-circle-question"></i><span class="link-text">FAQs</span></a>
      </li>

      <li>
        <a href="reviews"><i class="fa-solid fa-thumbs-up"></i><span class="link-text">Reviews</span></a>
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
