document.addEventListener("DOMContentLoaded", function () {

  const forms = document.querySelectorAll(".validate-form");
  if (!forms.length) return;

  /* CENTRAL VALIDATION LIBRARY */
  const validationRules = {
    required: {
      regex: /.+/,
      message: "This field is required"
    },
    name: {
      regex: /^[A-Za-z\s]{3,}$/,
      message: "Must be at least 3 letters"
    },
    city: {
      regex: /^[A-Za-z\s]{2,}$/,
      message: "Enter a valid city name"
    },
    price: {
      regex: /^[0-9]+(\.[0-9]{1,2})?$/,
      message: "Enter a valid price"
    },
    text10: {
    regex: /^[\s\S]{10,}$/,
    message: "Minimum 10 characters required"
    },
    text20: {
      regex: /^[\s\S]{20,}$/,
      message: "Minimum 20 characters required"
    },
    number: {
      regex: /^[0-9]+$/,
      message: "Only numbers allowed"
    },
    duration: {
      regex: /^.{3,}$/,
      message: "Enter valid duration (e.g. 7 Days)"
    }
  };

  forms.forEach(form => {

    const fields = form.querySelectorAll("[data-validate]");

    fields.forEach(field => {
      field.addEventListener("input", () => validateField(field));
    });

    form.addEventListener("submit", function (e) {

      let isValid = true;

      fields.forEach(field => {
        if (!validateField(field)) {
          isValid = false;
        }
      });

      if (!isValid) e.preventDefault();
    });

  });

  function validateField(field) {

    const ruleName = field.dataset.validate;
    const rule = validationRules[ruleName];

    if (!rule) return true;

    const value = field.value.trim();
    const group = field.closest(".form-group");
    const errorEl = group.querySelector(".error");

    if (!rule.regex.test(value)) {
      group.classList.add("error");
      if (errorEl) errorEl.textContent = rule.message;
      return false;
    } else {
      group.classList.remove("error");
      if (errorEl) errorEl.textContent = "";
      return true;
    }
  }

});