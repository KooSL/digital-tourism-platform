let currentIndex = 0;

function slideTestimonial(direction) {
  const track = document.getElementById("testimonialTrack");
  const cards = document.querySelectorAll(".testimonial-card");
  const visibleCards =
    window.innerWidth <= 576 ? 1 : window.innerWidth <= 992 ? 2 : 3;
  const cardWidth = cards[0].offsetWidth + 30;
  const maxIndex = cards.length - visibleCards;

  currentIndex += direction;

  if (currentIndex < 0) currentIndex = 0;
  if (currentIndex > maxIndex) currentIndex = maxIndex;

  track.style.transform = `translateX(-${currentIndex * cardWidth}px)`;
}

const testimonialtrack = document.getElementById("testimonialTrack");
const cards = document.querySelectorAll(".testimonial-card");

let testimonialindex = 0;
let visibleCards = 3; // desktop default

function updateVisibleCards() {
  if (window.innerWidth <= 576) {
    visibleCards = 1;
  } else if (window.innerWidth <= 992) {
    visibleCards = 2;
  } else {
    visibleCards = 3;
  }
}

function slideTestimonialsAuto() {
  updateVisibleCards();

  testimonialindex++;

  if (testimonialindex > cards.length - visibleCards) {
    testimonialindex = 0; // loop back smoothly
  }

  const cardWidth = cards[0].offsetWidth + 30; // card + gap
  testimonialtrack.style.transform = `translateX(-${testimonialindex * cardWidth}px)`;
}

window.addEventListener("resize", updateVisibleCards);
updateVisibleCards();

// anti-flicker.css starts this track at opacity:0 - reveal it now that
// it's in its correct initial position (index 0 = no transform needed).
if (testimonialtrack) {
  testimonialtrack.classList.add("slider-ready");
}

// Auto slide every 4 seconds
setInterval(slideTestimonialsAuto, 4000);
