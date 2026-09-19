let lastScrollTop = 0;
const scrollThreshold = 700;

const headerWrapper = document.querySelector(".header-wrapper");

window.addEventListener("scroll", function () {
  const currentScrollTop =
    window.pageYOffset || document.documentElement.scrollTop;

  if (currentScrollTop > lastScrollTop && currentScrollTop > scrollThreshold) {
    // Scrolling down → hide topbar
    headerWrapper.classList.add("topbar-hidden");
  } else {
    // Scrolling up → show topbar
    headerWrapper.classList.remove("topbar-hidden");
  }

  lastScrollTop = Math.max(currentScrollTop, 0);
});
