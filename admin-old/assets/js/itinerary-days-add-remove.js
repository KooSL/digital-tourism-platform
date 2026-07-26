function addItinerary() {
    const wrapper = document.getElementById('itinerary-wrapper');

    const row = document.createElement('div');
    row.classList.add('itinerary-row');

    row.innerHTML = `
        <div class="form-group">
            <input type="number" name="day_no[]" placeholder="Day 1" class="day-no">
            <small class="error"></small>
        </div>

        <div class="form-group">
            <input type="text" name="itinerary_title[]" placeholder="Title" class="it-title">
            <small class="error"></small>
        </div>

        <div class="form-group">
            <textarea name="itinerary_desc[]" placeholder="Description" class="it-desc"></textarea>
            <small class="error"></small>
        </div>

        <button type="button" class="remove-itinerary">Remove</button>
    `;

    wrapper.appendChild(row);
}

/* REMOVE ITINERARY (works for existing & dynamic rows) */
document.addEventListener('click', function (e) {
    if (e.target.classList.contains('remove-itinerary')) {
        const row = e.target.closest('.itinerary-row');
        row.remove();
    }
});
