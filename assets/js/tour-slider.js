document.querySelectorAll(".tour-slider-wrapper").forEach((wrapper) => {
  const track = wrapper.querySelector(".tour-slider-track");
  const nextBtn = wrapper.querySelector(".next");
  const prevBtn = wrapper.querySelector(".prev");
  const cards = wrapper.querySelectorAll(".tour-card");

  let index = 0;
  let startX = 0;
  let isDragging = false;

  function getVisibleCards() {
    if (window.innerWidth <= 600) return 1;
    if (window.innerWidth <= 1024) return 2;
    return 3;
  }

  if (cards.length <= getVisibleCards()) {
    track.classList.add("center");
  } else {
    // Only REVEAL the buttons when they're actually needed, instead of the
    // old approach of showing them by default then hiding a beat later
    // (which is what caused the visible flash on load).
    nextBtn.classList.add("slider-ready");
    prevBtn.classList.add("slider-ready");
  }

  function getCardWidth() {
    const card = wrapper.querySelector(".tour-card");
    const gap = 25;
    return card.offsetWidth + gap;
  }

  function updateSlider() {
    const cardWidth = getCardWidth();
    track.style.transform = `translateX(-${index * cardWidth}px)`;
  }

  // Position is now correct (index 0 = translateX(0), same as the CSS
  // default) - safe to fade the track in. anti-flicker.css keeps it at
  // opacity:0 until this class is added, so all the class/display
  // decisions above happened invisibly.
  track.classList.add("slider-ready");

  // Button navigation
  nextBtn.addEventListener("click", () => {
    const visibleCards = getVisibleCards();
    if (index < cards.length - visibleCards) {
      index++;
      updateSlider();
    }
  });

  prevBtn.addEventListener("click", () => {
    if (index > 0) {
      index--;
      updateSlider();
    }
  });

  // Touch swipe
  track.addEventListener("touchstart", (e) => {
    startX = e.touches[0].clientX;
    isDragging = true;
  });

  track.addEventListener("touchmove", (e) => {
    if (!isDragging) return;
  });

  track.addEventListener("touchend", (e) => {
    if (!isDragging) return;

    let endX = e.changedTouches[0].clientX;
    let diff = startX - endX;

    if (Math.abs(diff) > 50) {
      const visibleCards = getVisibleCards();

      if (diff > 0 && index < cards.length - visibleCards) {
        index++; // swipe left
      } else if (diff < 0 && index > 0) {
        index--; // swipe right
      }

      updateSlider();
    }

    isDragging = false;
  });
});
