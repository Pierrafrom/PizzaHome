const max = 8;

document.addEventListener("DOMContentLoaded", function () {
  // Select all cancel buttons and password toggle icons in the DOM.
  const cancelButtons = document.querySelectorAll(".cancel-btn");
  const toggleIcons = document.querySelectorAll(".toggle-password");
  const duplicateButtons = document.querySelectorAll(".duplicate-btn");

  // Toggle password visibility when the toggle icon is clicked.
  toggleIcons.forEach(function (icon) {
    icon.addEventListener("click", function () {
      const input = this.closest(".form-group").querySelector("input");

      const isPassword = input.type === "password";
      input.type = isPassword ? "text" : "password";

      const eyeOpenIcon =
        this.closest(".form-group").querySelector(".eye-open");
      const eyeClosedIcon =
        this.closest(".form-group").querySelector(".eye-closed");

      if (isPassword) {
        eyeOpenIcon.classList.remove("hide");
        eyeOpenIcon.classList.add("show-flex");
        eyeClosedIcon.classList.add("hide");
        eyeClosedIcon.classList.remove("show-flex");
      } else {
        eyeOpenIcon.classList.add("hide");
        eyeOpenIcon.classList.remove("show-flex");
        eyeClosedIcon.classList.remove("hide");
        eyeClosedIcon.classList.add("show-flex");
      }
    });
  });

  // Add click event listeners for all cancel buttons.
  cancelButtons.forEach(function (cancelButton) {
    cancelButton.addEventListener("click", function (event) {
      event.preventDefault(); // Prevent the default button action.
      // Retrieve the redirect URL value from a hidden input field.
      let redirectUrl = document.querySelector('input[name="redirect"]').value;
      // Redirect to the provided URL or to the home page if no URL is provided.
      window.location.href = redirectUrl
        ? decodeURIComponent(redirectUrl)
        : "/";
    });
  });

  duplicateButtons.forEach(function (button) {
    button.addEventListener("click", function () {
      const elementToDuplicate = this.parentNode.previousElementSibling;
      duplicateElement(elementToDuplicate);
      updateDeleteButtonsVisibility();
    });
  });

  updateDeleteSelector();
  updateDeleteButtonsVisibility();
});

/**
 * Validates all input fields of a given form.
 * Each input field is checked to ensure it is not empty.
 * If an input is empty, it is marked as invalid.
 *
 * @param {HTMLFormElement} form - The form element to be validated.
 * @returns {boolean} True if all form inputs are valid, otherwise false.
 */
function validateForm(form) {
  let isValid = true;
  form.querySelectorAll("input").forEach((input) => {
    if (!input.value.trim()) {
      isValid = false;
      input.classList.add("invalid"); // Add 'invalid' class to empty input fields.
    } else {
      input.classList.remove("invalid"); // Remove 'invalid' class if input is filled.
    }
  });
  return isValid;
}

/**
 * Duplique un élément HTML spécifié et l'ajoute à la page.
 *
 * @param {HTMLElement} element - L'élément HTML à dupliquer.
 */
function duplicateElement(element) {
  if (!element || !(element instanceof HTMLElement)) {
      console.error("Invalid element provided for duplication.");
      return;
  }

  if (typeof max !== "undefined") {
      if (document.querySelectorAll(".toDuplicate").length >= max) {
          return;
      } else if (document.querySelectorAll(".toDuplicate").length === max - 1) {
          document.querySelectorAll(".duplicate-btn").forEach(function (button) {
              button.classList.add("disabled");
          });
          document.querySelectorAll(".add-btn").forEach(function (button) {
              button.classList.add("disabled");
          });
      }
  }

  const clonedElement = element.cloneNode(true);
  clonedElement.removeAttribute("id");
  clonedElement.querySelectorAll("[id]").forEach((el) => el.removeAttribute("id"));

  // Modify name attributes
  clonedElement.querySelectorAll("[name]").forEach((input) => {
      let name = input.getAttribute("name");
      let newName = modifyNameAttribute(name);
      input.setAttribute("name", newName);
  });

  element.parentNode.insertBefore(clonedElement, element.nextSibling);
  updateDeleteSelector();
}

function modifyNameAttribute(name) {
  // Check if name ends with a number
  let match = name.match(/(\d+)$/);
  if (match) {
      let number = parseInt(match[0], 10);
      return name.replace(/(\d+)$/, number + 1);
  } else {
      return name + '-1';
  }
}

function updateDeleteSelector() {
  document.querySelectorAll(".delete-btn").forEach(function (button) {
    button.addEventListener("click", function () {
      this.parentNode.parentNode.remove();
      updateDeleteButtonsVisibility();
    });
  });
}

function updateDeleteButtonsVisibility() {
  // Comptez le nombre d'éléments duplicables
  const deleteButtons = document.querySelectorAll(".delete-btn");
  const numberOfElements = document.querySelectorAll(".toDuplicate").length;

  deleteButtons.forEach(function (button) {
    if (numberOfElements > 1) {
      button.classList.remove("hide");
      button.classList.add("show-flex");
    } else {
      button.classList.add("hide");
      button.classList.remove("show-flex");
    }

    // si on a les boutons de duplication désactivés, on les réactive
    if (numberOfElements < max) {
      document.querySelectorAll(".duplicate-btn").forEach(function (button) {
        button.classList.remove("disabled");
      });
    }
  });

  // Ré-attachez les écouteurs d'événements pour les nouveaux boutons de suppression
  updateDeleteSelector();
}
