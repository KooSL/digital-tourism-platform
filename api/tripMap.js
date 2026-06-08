  var map = L.map('map').setView(
    [latitude, longitude],
    10
  );

  L.tileLayer(
    'https://tile.openstreetmap.org/{z}/{x}/{y}.png'
  ).addTo(map);

  L.marker([latitude, longitude])
    .addTo(map)
    .bindPopup('Tour Destination');