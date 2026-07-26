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

</body>

</html>