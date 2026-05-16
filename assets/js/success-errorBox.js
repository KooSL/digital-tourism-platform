document.addEventListener("DOMContentLoaded", () => {
  handleBox("successBox");
  handleBox("errorBox");

  function handleBox(id) {
    const box = document.getElementById(id);

    if (!box) return;

    setTimeout(() => {
      box.style.transition = "opacity 0.5s ease, transform 0.5s ease";
      box.style.opacity = "0";
      box.style.transform = "translateY(-10px)";

      setTimeout(() => {
        box.remove();
      }, 500);
    }, 4000);

    if (window.history.replaceState) {
      let url = window.location.href;

      url = url
        .replace(/([?&])(success|error)=1(&|$)/, "$1")
        .replace(/[?&]$/, "");

      window.history.replaceState({}, document.title, url);
    }
  }
});
