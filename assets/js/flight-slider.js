const flightTrack = document.getElementById("flightTrack");
const flightCards = document.querySelectorAll(".flight-card");
const flightPrevBtn = document.getElementById("flightPrevBtn");
const flightNextBtn = document.getElementById("flightNextBtn");

let flightIndex = 0;

function updateFlightSlider() {
  flightTrack.style.transform = `translateX(-${flightIndex * 100}%)`;
}

// anti-flicker.css starts this track at opacity:0 so it's never visible
// mid-setup; reveal it now that listeners are wired up.
if (flightTrack) {
  flightTrack.classList.add("slider-ready");
}

flightNextBtn.addEventListener("click", () => {
  if (flightIndex < flightCards.length - 1) {
    flightIndex++;
    updateFlightSlider();
  }
});

flightPrevBtn.addEventListener("click", () => {
  if (flightIndex > 0) {
    flightIndex--;
    updateFlightSlider();
  }
});
