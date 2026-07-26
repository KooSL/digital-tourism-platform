/* ==========================================================
   Shared behaviour for single-page CRUD modules:
   - open "Add" modal
   - open "Edit" modal and populate it from the row's
     data-field="..." attributes (no extra page load needed)
   - close modal (button, overlay click, ESC key)
   - auto-submit the filter form when a <select> changes
   ========================================================== */

function openAddModal() {
  var modal = document.getElementById('addModal');
  if (modal) modal.classList.add('active');
}

function openEditModal(button) {
  var modal = document.getElementById('editModal');
  if (!modal) return;

  var data = button.dataset;

  Object.keys(data).forEach(function (key) {
    if (key === 'id') return;

    var field = modal.querySelector('[name="' + key + '"]');
    if (!field) return;

    if (field.tagName === 'SELECT') {
      field.value = data[key];
    } else if (field.type === 'file') {
      // skip - files are never pre-filled
    } else {
      field.value = data[key];
    }
  });

  // Set the hidden id field used by the update handler
  var idField = modal.querySelector('[name="id"]');
  if (idField) idField.value = data.id;

  // Tours: rebuild the itinerary day rows from JSON stashed in data-itinerary
  if (data.itinerary !== undefined && typeof loadItinerary === 'function') {
    try {
      loadItinerary('edit-itinerary-wrapper', JSON.parse(data.itinerary));
    } catch (e) {
      loadItinerary('edit-itinerary-wrapper', []);
    }
  }

  // Update current-image preview if present
  var preview = modal.querySelector('.current-image img');
  if (preview && data.image) {
    preview.src = data.imagePath ? data.imagePath : preview.src;
  }

  modal.classList.add('active');
}

function closeModal(id) {
  var modal = document.getElementById(id);
  if (modal) modal.classList.remove('active');
}

document.addEventListener('click', function (e) {
  if (e.target.classList && e.target.classList.contains('crud-modal-overlay')) {
    e.target.classList.remove('active');
  }
});

document.addEventListener('keydown', function (e) {
  if (e.key === 'Escape') {
    document.querySelectorAll('.crud-modal-overlay.active').forEach(function (m) {
      m.classList.remove('active');
    });
  }
});

document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('.auto-submit').forEach(function (el) {
    el.addEventListener('change', function () {
      el.form.submit();
    });
  });

  // Re-open a modal automatically if the server flagged a validation error
  var reopen = document.body.getAttribute('data-reopen-modal');
  if (reopen) {
    var m = document.getElementById(reopen);
    if (m) m.classList.add('active');
  }
});
