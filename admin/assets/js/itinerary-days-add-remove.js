function addItinerary(wrapperId, day, title, desc) {
    // Back-compat: allow addItinerary() with no args (defaults to the Add-modal wrapper),
    // and addItinerary(day, title, desc) is no longer valid — wrapperId is now required
    // as the first argument so we always know which modal's list to append to.
    if (wrapperId === undefined) wrapperId = 'itinerary-wrapper';

    const wrapper = document.getElementById(wrapperId);
    if (!wrapper) return;

    const row = document.createElement('div');
    row.classList.add('itinerary-row');

    day = day !== undefined && day !== null ? day : '';
    title = title !== undefined && title !== null ? title : '';
    desc = desc !== undefined && desc !== null ? desc : '';

    row.innerHTML = `
        <div class="form-group">
            <input type="number" name="day_no[]" placeholder="Day 1" class="day-no" value="${day}">
            <small class="error"></small>
        </div>

        <div class="form-group">
            <input type="text" name="itinerary_title[]" placeholder="Title" class="it-title" value="${String(title).replace(/"/g, '&quot;')}">
            <small class="error"></small>
        </div>

        <div class="form-group">
            <textarea name="itinerary_desc[]" placeholder="Description" class="it-desc">${desc}</textarea>
            <small class="error"></small>
        </div>

        <button type="button" class="remove-itinerary">Remove</button>
    `;

    wrapper.appendChild(row);
}

/* Rebuild an itinerary wrapper's rows from a JSON array (used when opening the Tours edit modal) */
function loadItinerary(wrapperId, itineraries) {
    const wrapper = document.getElementById(wrapperId);
    if (!wrapper) return;
    wrapper.innerHTML = '';

    if (!itineraries || itineraries.length === 0) {
        addItinerary(wrapperId);
        return;
    }

    itineraries.forEach(function (it) {
        addItinerary(wrapperId, it.day_number, it.title, it.description);
    });
}

/* REMOVE ITINERARY (works for existing & dynamic rows) */
document.addEventListener('click', function (e) {
    if (e.target.classList.contains('remove-itinerary')) {
        const row = e.target.closest('.itinerary-row');
        row.remove();
    }
});
