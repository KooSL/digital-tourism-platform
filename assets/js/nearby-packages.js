/**
 * nearby-packages.js
 * ----------------------------------------------------------------------
 * Asks the browser for the visitor's GPS location, then calls
 * api/nearby-user.php to fetch tours near them (Haversine distance,
 * computed server-side in MySQL). Renders results into #nearbyGrid.
 *
 * Falls back to a friendly message if the user denies location access
 * or their browser doesn't support geolocation - the "Nearby This
 * Destination" section (rendered server-side, no JS required) still
 * covers that case.
 * ----------------------------------------------------------------------
 */
(function () {
  const grid = document.getElementById("nearbyGrid");
  const statusEl = document.getElementById("nearbyStatus");
  if (!grid) return;

  const excludeId = grid.dataset.excludeId || 0;

  function renderCards(tours) {
    if (!tours.length) {
      statusEl.textContent =
        "No packages found near your current location yet.";
      return;
    }

    statusEl.style.display = "none";

    grid.innerHTML = tours
      .map(function (t) {
        return `
        <div class="recommend-card">
          <img src="admin/uploads/images/tours/${t.banner_image}" alt="${t.title}">
          <h4>${t.title}</h4>
          <div class="recommend-info">
            <p><i class="fa-solid fa-clock"></i> ${t.duration}</p>
            <p class="recommend-distance"><i class="fa-solid fa-location-dot"></i> ${t.distance_km} km away</p>
          </div>
          <p class="current-price recommend-price">
            NPR ${t.price} <span>| USD $${t.price_usd} PP</span>
          </p>
          <a href="tour-details?id=${t.id}">View</a>
        </div>
      `;
      })
      .join("");
  }

  if (!("geolocation" in navigator)) {
    statusEl.textContent = "Your browser doesn't support location detection.";
    return;
  }

  statusEl.textContent = "Detecting your location...";

  navigator.geolocation.getCurrentPosition(
    function (pos) {
      const lat = pos.coords.latitude;
      const lng = pos.coords.longitude;

      fetch(`api/nearby-user?lat=${lat}&lng=${lng}&exclude=${excludeId}`)
        .then(function (res) {
          if (!res.ok) throw new Error("Request failed");
          return res.json();
        })
        .then(function (data) {
          renderCards(data.tours || []);
        })
        .catch(function () {
          statusEl.textContent = "Couldn't load nearby packages right now.";
        });
    },
    function () {
      statusEl.textContent =
        "Location access denied. Showing nearby packages by destination instead.";
    },
    { enableHighAccuracy: false, timeout: 8000, maximumAge: 300000 },
  );
})();
