const input = document.getElementById('tourSearch');
const resultsBox = document.getElementById('searchResults');

input.addEventListener('keyup', () => {
  const query = input.value.trim();

  if(query.length < 2) {
    resultsBox.innerHTML = '';
    return;
  }

  fetch(`api/search-tours?q=${encodeURIComponent(query)}`)
    .then(res => res.json())
    .then(data => {
      resultsBox.innerHTML = '';

      data.forEach(tour => {
        const div = document.createElement('div');
        div.classList.add('search-item');
        div.textContent = tour.title;

        div.onclick = () => {
          // redirect using title
          window.location.href =
            `tour-details?id=${encodeURIComponent(tour.id)}`;
        };

        resultsBox.appendChild(div);
      });
    });
});