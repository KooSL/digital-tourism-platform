const input = document.getElementById('tripsearch');
const resultsBox = document.getElementById('searchResults');

input.addEventListener('keyup', () => {
  const query = input.value.trim();

  if(query.length < 2) {
    resultsBox.innerHTML = '';
    return;
  }

  fetch(`api/search-trips?q=${encodeURIComponent(query)}`)
    .then(res => res.json())
    .then(data => {
      resultsBox.innerHTML = '';

      data.forEach(trip => {
        const div = document.createElement('div');
        div.classList.add('search-item');
        div.textContent = trip.title;

        div.onclick = () => {
          // redirect using title
          window.location.href =
            `trip-details?trip=${encodeURIComponent(trip.slug)}`;
        };

        resultsBox.appendChild(div);
      });
    });
});