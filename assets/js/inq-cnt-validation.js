document.addEventListener("DOMContentLoaded", () => {

  const form = document.getElementById("userForm");
  if (!form) return;

  const fields = {
    name: {
      regex: /^[A-Za-z\s]{3,}$/,
      message: "Name must contain only letters (min 3 characters)"
    },
    email: {
      regex: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
      message: "Enter a valid email address"
    },
    phone: {
      regex: /^[0-9]{7,15}$/,
      message: "Phone must be 7–15 digits only"
    },
    message: {
      regex: /^[\s\S]{10,}$/,
      message: "Inquiry or Message must be at least 10 characters"
    }
  };

  const activeFields = {};

  // Detect available fields
  Object.keys(fields).forEach(key => {
    const input = document.getElementById(key);

    if (input) {
      activeFields[key] = {
        ...fields[key],
        input: input
      };

      // Live validation
      input.addEventListener("input", () => {
        validateField(activeFields[key]);
      });
    }
  });

  // Submit validation
  form.addEventListener("submit", (e) => {
    let valid = true;

    Object.values(activeFields).forEach(field => {
      if (!validateField(field)) {
        valid = false;
      }
    });

    if (!valid) {
      e.preventDefault();
    }
  });

  function validateField(field) {
    const value = field.input.value.trim();
    const formGroup = field.input.parentElement;
    const errorEl = formGroup.querySelector("small.error");

    // Required check
    if (value === "") {
      formGroup.classList.add("error");
      errorEl.textContent = "This field is required";
      return false;
    }

    // Regex validation
    if (field.regex && !field.regex.test(value)) {
      formGroup.classList.add("error");
      errorEl.textContent = field.message;
      return false;
    }

    // Valid
    formGroup.classList.remove("error");
    errorEl.textContent = "";
    return true;
  }

});