document.addEventListener("DOMContentLoaded", () => {

  const form = document.querySelector(".admin-form");

  const fields = {
    title: {
      input: document.getElementById("title"),
      regex: /^.{3,}$/,
      message: "Title must be at least 3 characters"
    },
    duration: {
      input: document.getElementById("duration"),
      regex: /^.{3,}$/,
      message: "Duration is required (e.g. 7 Days)"
    },
    price: {
      input: document.getElementById("price"),
      regex: /^[0-9]+(\.[0-9]{1,2})?$/,
      message: "Enter a valid price"
    },
    price_usd: {
      input: document.getElementById("price_usd"),
      regex: /^[0-9]+(\.[0-9]{1,2})?$/,
      message: "Enter a valid price"
    },
    overview: {
      input: document.getElementById("overview"),
      regex: /^.{20,}$/,
      message: "Overview must be at least 20 characters"
    },
    highlights: {
      input: document.getElementById("highlights"),
      regex: /^.{10,}$/,
      message: "Highlights are required"
    },
    includes: {
      input: document.getElementById("includes"),
      regex: /^.{10,}$/,
      message: "Includes are required"
    },
    excludes: {
      input: document.getElementById("excludes"),
      regex: /^.{10,}$/,
      message: "Excludes are required"
    }
  };

  /* LIVE VALIDATION */
  Object.values(fields).forEach(field => {
    field.input.addEventListener("input", () => {
      validateField(field);
    });
  });

  /* SUBMIT VALIDATION */
  form.addEventListener("submit", (e) => {
    let isValid = true;

    Object.values(fields).forEach(field => {
      if (!validateField(field)) {
        isValid = false;
      }
    });

    if (!isValid) {
      e.preventDefault(); 
    }
  });

  function validateField(field) {
    const value = field.input.value.trim();
    const group = field.input.closest(".form-group");
    const errorEl = group.querySelector("small.error");

    if (!field.regex.test(value)) {
      group.classList.add("error");
      errorEl.textContent = field.message;
      return false;
    } else {
      group.classList.remove("error");
      errorEl.textContent = "";
      return true;
    }
  }

});
