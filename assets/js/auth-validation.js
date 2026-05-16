document.addEventListener("DOMContentLoaded", () => {

  const form = document.querySelector("form");
  if (!form) return;

  const fields = {
    name: {
      regex: /^[A-Za-z\s]{3,}$/,
      message: "Name must be at least 3 letters"
    },
    email: {
      regex: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
      message: "Enter valid email"
    },
    phone: {
      regex: /^\d{10}$/,
      message: "Phone must be 10 digits"
    },
    password: {
      regex: /^.{8,}$/,
      message: "Password must be at least 8 characters"
    },
    confirm_password: {
      match: "password",
      message: "Passwords do not match"
    }
  };

  const activeFields = {};

  // Detect fields in current form (login/register)
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
      if (!validateField(field)) valid = false;
    });

    if (!valid) e.preventDefault();
  });

  function validateField(field) {
    const value = field.input.value.trim();
    const group = field.input.parentElement;
    const error = group.querySelector("small.error");

    // Required check
    if (value === "") {
      group.classList.add("error");
      error.textContent = "This field is required";
      return false;
    }

    // Confirm password
    if (field.match) {
      const matchValue = document.getElementById(field.match).value;
      if (value !== matchValue) {
        group.classList.add("error");
        error.textContent = field.message;
        return false;
      }
    }

    // Regex validation
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