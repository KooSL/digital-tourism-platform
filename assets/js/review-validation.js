document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("reviewForm");

  if (!form) return;

  const fields = {
    name: {
      regex: /^[A-Za-z\s]{3,50}$/,
      message: "Name must contain only letters (min 3 characters)",
    },

    review: {
      regex: /^[A-Za-z0-9\s.,!?'-]{10,500}$/,
      message: "Review must be 10-500 characters",
    },
  };

  const activeFields = {};

  // detect inputs
  Object.keys(fields).forEach((key) => {
    const input = form.querySelector(`[name="${key}"]`);

    if (input) {
      activeFields[key] = {
        ...fields[key],
        input: input,
      };

      // live validation
      input.addEventListener("input", () => {
        validateField(activeFields[key]);
      });
    }
  });

  // rating validation
  const ratings = form.querySelectorAll("input[name='rating']");

  ratings.forEach((rating) => {
    rating.addEventListener("change", () => {
      const error = form
        .querySelector(".star-rating")
        .closest(".form-group")
        .querySelector("small.error");

      error.textContent = "";
    });
  });

  // submit validation
  form.addEventListener("submit", (e) => {
    let valid = true;

    Object.values(activeFields).forEach((field) => {
      if (!validateField(field)) {
        valid = false;
      }
    });

    // check rating

    const selectedRating = form.querySelector("input[name='rating']:checked");

    if (!selectedRating) {
      const error = form
        .querySelector(".star-rating")
        .closest(".form-group")
        .querySelector("small.error");

      error.textContent = "Please select rating";

      valid = false;
    }

    if (!valid) {
      e.preventDefault();
    }
  });

  function validateField(field) {
    const value = field.input.value.trim();

    const group = field.input.parentElement;

    const error = group.querySelector("small.error");

    // required

    if (value === "") {
      group.classList.add("error");

      error.textContent = "This field is required";

      return false;
    }

    // regex

    if (field.regex && !field.regex.test(value)) {
      group.classList.add("error");

      error.textContent = field.message;

      return false;
    }

    group.classList.remove("error");

    error.textContent = "";

    return true;
  }
});
