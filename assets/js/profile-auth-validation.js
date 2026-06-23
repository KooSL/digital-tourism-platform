document.addEventListener("DOMContentLoaded", () => {
  const forms = document.querySelectorAll("form");

  forms.forEach((form) => {
    const fields = {
      name: {
        regex: /^[A-Za-z\s]{3,}$/,
        message: "Name must be at least 3 letters",
      },

      email: {
        regex: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
        message: "Enter valid email",
      },

      phone: {
        regex: /^\+?\d{10,15}$/,
        message: "Phone must be between 10 and 15 digits",
      },

      password: {
        regex: /^.{8,}$/,
        message: "Password must be at least 8 characters",
      },

      confirm_password: {
        match: "password",
        message: "Passwords do not match",
      },

      address: {
        regex: /^.{5,}$/,
        message: "Address must be at least 5 characters",
      },
    };

    let activeFields = {};

    // Detect only fields inside current form

    Object.keys(fields).forEach((key) => {
      const input =
        form.querySelector("#" + key) ||
        form.querySelector("[name='" + key + "']");

      if (input) {
        activeFields[key] = {
          ...fields[key],
          input: input,
        };

        input.addEventListener("input", () => {
          validateField(activeFields[key]);
        });
      }
    });

    // Submit only current form

    form.addEventListener("submit", (e) => {
      let valid = true;

      Object.values(activeFields).forEach((field) => {
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

      const group = field.input.closest(".form-group");

      const error = group.querySelector(".error");

      if (value === "") {
        group.classList.add("error");

        error.textContent = "This field is required";

        return false;
      }

      if (field.match) {
        const matchInput = form.querySelector("#" + field.match);

        if (matchInput && value !== matchInput.value) {
          group.classList.add("error");

          error.textContent = field.message;

          return false;
        }
      }

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
});
