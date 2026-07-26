document.addEventListener("DOMContentLoaded", () => {
  const form = document.querySelector(".admin-form");

  function validateItineraryRow(row) {
    let valid = true;

    const dayInput = row.querySelector(".day-no");
    const titleInput = row.querySelector(".it-title");
    const descInput = row.querySelector(".it-desc");

    valid &= validateField(
      dayInput,
      /^[1-9][0-9]*$/,
      "Day must be a positive number"
    );

    valid &= validateField(
      titleInput,
      /^.{3,}$/,
      "Title must be at least 3 characters"
    );

    valid &= validateField(
      descInput,
      /^[\s\S]{10,}$/,
      "Description must be at least 10 characters"
    );

    return !!valid;
  }

  function validateField(input, regex, message) {
    const group = input.closest(".form-group");
    const errorEl = group.querySelector(".error");
    const value = input.value.trim();

    if (!regex.test(value)) {
      group.classList.add("error");
      errorEl.textContent = message;
      return false;
    } else {
      group.classList.remove("error");
      errorEl.textContent = "";
      return true;
    }
  }

  document.addEventListener("input", (e) => {
    if (
      e.target.classList.contains("day-no") ||
      e.target.classList.contains("it-title") ||
      e.target.classList.contains("it-desc")
    ) {
      const row = e.target.closest(".itinerary-row");
      validateItineraryRow(row);
    }
  });

  form.addEventListener("submit", (e) => {
    let isValid = true;

    const rows = document.querySelectorAll(".itinerary-row");

    rows.forEach((row) => {
      if (!validateItineraryRow(row)) {
        isValid = false;
      }
    });

    // if (!isValid) {
    //   e.preventDefault();
    //   alert("Please fix itinerary errors before submitting.");
    // }
  });
});
          