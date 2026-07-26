<footer class="admin-footer">
  <p>© <?php echo date('Y'); ?> Digital Tourism Platform. All rights reserved.</p>

  <script src="assets/js/firebase-admin.js"></script>
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

      <button id="confirmBtn" class="confirm">
        Confirm
      </button>

    </div>

  </div>

</div>

<script>
  // The page title lives in the header, not on the page itself.
  // Each page still starts with <h2>Title ...optional extra markup</h2> —
  // we lift the title text into the header and blank it out on the page,
  // leaving any nested elements (like a "back to albums" link) in place.
  document.addEventListener('DOMContentLoaded', function () {
    var pageHeading = document.querySelector('.admin-content h2');
    var headerHeading = document.getElementById('pageTitle');
    if (pageHeading && headerHeading) {
      var node = pageHeading.firstChild;
      var text = node ? node.textContent.trim() : pageHeading.textContent.trim();
      if (text) {
        headerHeading.textContent = text;
        if (node && node.nodeType === Node.TEXT_NODE) {
          node.textContent = '';
        } else {
          pageHeading.textContent = '';
        }
        if (!pageHeading.hasChildNodes() || pageHeading.textContent.trim() === '') {
          pageHeading.style.display = pageHeading.children.length ? '' : 'none';
        }
      }
    }

    var currentFile = location.pathname.split('/').pop() || 'dashboard';
    document.querySelectorAll('.sidebar-menu a[href]').forEach(function (link) {
      var href = link.getAttribute('href');
      if (href && href !== '' && href === currentFile) {
        link.classList.add('active');
        var parentSubmenu = link.closest('.submenu');
        if (parentSubmenu) {
          var parentLink = parentSubmenu.previousElementSibling;
          if (parentLink) parentLink.classList.add('active');
        }
      }
    });

    // On the mobile icon-only rail, submenus open on tap instead of hover.
    document.querySelectorAll('.has-submenu > a').forEach(function (trigger) {
      trigger.addEventListener('click', function (e) {
        if (window.innerWidth > 768) return;
        e.preventDefault();
        var parent = trigger.closest('.has-submenu');
        var wasOpen = parent.classList.contains('open');
        document.querySelectorAll('.has-submenu.open').forEach(function (el) {
          el.classList.remove('open');
        });
        if (!wasOpen) parent.classList.add('open');
      });
    });

    document.addEventListener('click', function (e) {
      if (window.innerWidth > 768) return;
      if (!e.target.closest('.has-submenu')) {
        document.querySelectorAll('.has-submenu.open').forEach(function (el) {
          el.classList.remove('open');
        });
      }
    });
  });
</script>

</body>

</html>
