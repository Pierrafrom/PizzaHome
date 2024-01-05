// Import the CustomAlert module from CustomAlert.js
import { CustomAlert } from "./CustomAlert.js";

/**
 * Sets up the initial tab display when the window is loaded.
 * It shows either the login or sign-in tab based on the URL hash.
 */
window.onload = function () {
  // Call showTab function with the current location hash or default to "#signin"
  showTab(location.hash || "#signin");
};

/**
 * Shows the appropriate tab (login or sign-in) based on the provided hash.
 *
 * @param {string} hash - The hash part of the URL (e.g., "#login" or "#signin").
 *                        If the hash is not provided or does not match expected values,
 *                        the function defaults to showing the sign-in tab.
 */
function showTab(hash) {
  // Get the login and sign-in tab elements from the DOM
  const loginTab = document.getElementById("login");
  const signinTab = document.getElementById("signin");
  // Get the tab link elements from the DOM
  const loginTabLink = document.getElementById("login-tab");
  const signinTabLink = document.getElementById("signin-tab");

  // Default to sign-in tab if hash is not provided or does not match expected values
  if (!hash || (hash !== "#login" && hash !== "#signin")) {
    hash = "#signin";
  }

  // If the hash is "#login", display the login tab and update tab link styles
  if (hash === "#login") {
    loginTab.classList.add("show-flex");
    loginTab.classList.remove("hide");
    signinTab.classList.add("hide");
    signinTab.classList.remove("show-flex");
    if (loginTabLink) loginTabLink.classList.add("active-tab");
    if (signinTabLink) signinTabLink.classList.remove("active-tab");
  } else {
    // Otherwise, display the sign-in tab and update tab link styles
    signinTab.classList.add("show-flex");
    signinTab.classList.remove("hide");
    loginTab.classList.add("hide");
    loginTab.classList.remove("show-flex");
    if (signinTabLink) signinTabLink.classList.add("active-tab");
    if (loginTabLink) loginTabLink.classList.remove("active-tab");
  }
}

/**
 * This script is responsible for handling form submissions, password visibility toggling,
 * and cancellation actions for login and sign-in forms.
 *
 * It includes event listeners for form submission, which validate the form data before
 * submission. For the sign-in form, it also checks password matching and complexity.
 * Additionally, it manages the toggle of password visibility and the cancellation action
 * based on a redirect URL.
 */

// Event listener for when the DOM content is fully loaded.
document.addEventListener("DOMContentLoaded", function () {
  // Select the login and sign-in forms.
  const loginForm = document.querySelector(".login-form");
  const signinForm = document.querySelector(".signin-form");

  // Add submit event listener for the login form.
  loginForm.addEventListener("submit", function (event) {
    event.preventDefault(); // Prevent the default form submission action.
    if (validateForm(this)) {
      // Validate form fields.
      verifyPassword(
        this.querySelector('input[name="email"]').value,
        this.querySelector('input[name="password"]').value,
        (isSuccessful) => {
          if (isSuccessful) {
            this.submit(); // Submit the form only if the credentials are valid
          } else {
            showMessage("Incorrect password or email.", CustomAlert.Type.ERROR);
          }
        }
      );
    } else {
      // Show an error message if validation fails.
      showMessage("Please fill out all fields.", CustomAlert.Type.ERROR);
    }
  });

  // Add submit event listener for the sign-in form.
  signinForm.addEventListener("submit", function (event) {
    event.preventDefault(); // Prevent the default form submission action.
    let fieldsFilled = validateForm(this);
    if (!fieldsFilled) {
      // Show error message if password is not complex enough.
      showMessage("Please fill out all fields.", CustomAlert.Type.ERROR);
      return;
    }
    let samePassword = checkPasswords(this);
    if (!samePassword) {
      // Show error message if passwords don't match.
      showMessage("Passwords do not match.", CustomAlert.Type.ERROR);
      return;
    }
    let passwordOK = validatePasswordComplexity(this);
    if (!passwordOK) {
      showMessage(
        "Password must be at least 8 characters long and include at least one uppercase " +
          "letter, one number, and one special character.",
        CustomAlert.Type.ERROR
      );
      return;
    }
    checkEmailExists(
      this.querySelector('input[name="email"]').value,
      (isSuccessful) => {
        if (isSuccessful) {
          this.submit(); // Submit the form only if the email is valid
        } else {
          showMessage("Email already exists.", CustomAlert.Type.ERROR);
        }
      }
    );
  });
});

/**
 * Checks if the password and confirm password fields in a form match.
 * If they do not match, an error message is displayed.
 *
 * @param {HTMLFormElement} form - The form containing the password fields.
 * @returns {boolean} True if passwords match, otherwise false.
 */
function checkPasswords(form) {
  const password = form.querySelector('input[name="password"]').value;
  const confirmPassword = form.querySelector(
    'input[name="confirm_password"]'
  ).value;
  return password === confirmPassword;
}

/**
 * Validates the complexity of the password in a form.
 * The password must be at least 8 characters long and include at least one uppercase letter,
 * one number, and one special character.
 *
 * @param {HTMLFormElement} form - The form containing the password to be validated.
 * @returns {boolean} True if the password meets the complexity requirements, otherwise false.
 */
function validatePasswordComplexity(form) {
  const password = form.querySelector('input[name="password"]').value;
  const passwordRegex =
    /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;

  return passwordRegex.test(password);
}

/**
 * Displays a custom alert message with a specified type.
 *
 * @param {string} message - The message to be displayed in the alert.
 * @param {CustomAlert.Type} type - The type of the alert (e.g., error, info).
 */
function showMessage(message, type) {
  const alert = new CustomAlert();
  alert.show(message, type); // Show the custom alert with the provided message and type.
}

/**
 * Verifies the user's password by making an API call.
 *
 * This function sends a POST request to the '/api/verifyPassword' endpoint with the user's
 * email and password. Upon receiving the response, it invokes the callback function with
 * a boolean value indicating the success or failure of the password verification.
 *
 * @param {string} email - The user's email address.
 * @param {string} password - The user's password.
 * @param {function} callback - A callback function that is called with the result of the verification.
 *                              It takes a single boolean argument: `true` if the verification succeeded,
 *                              `false` otherwise.
 */
function verifyPassword(email, password, callback) {
  const data = { email, password };

  fetch("/api/verifyPassword", {
    method: "POST",
    body: JSON.stringify(data),
    headers: {
      "Content-Type": "application/json",
      Accept: "*/*",
    },
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        callback(true);
      } else {
        callback(false);
      }
    })
    .catch((error) => {
      console.error(error);
      callback(false);
    });
}

/**
 * Checks if an email exists by making a POST request to a server endpoint.
 *
 * This function sends the provided email to an API endpoint '/api/checkEmailExists'
 * using a POST request. The server is expected to respond with a JSON object
 * indicating whether the email exists or not. The callback function is then
 * called with a boolean value based on the server's response. In case of a fetch
 * error, the callback is called with false.
 *
 * @param {string} email - The email address to check for existence.
 * @param {function} callback - A callback function that is called with the result.
 */
function checkEmailExists(email, callback) {
  // Prepare the data to be sent
  const data = { email };

  // Make a POST request to the server
  fetch("/api/checkEmailExists", {
    method: "POST",
    body: JSON.stringify(data),
    headers: {
      "Content-Type": "application/json",
      Accept: "*/*",
    },
  })
    // Parse the response to JSON
    .then((response) => response.json())
    .then((data) => {
      console.log(data);
      // Call the callback based on the server's response
      if (data.success) {
        callback(false); // Email does not exist
      } else {
        callback(true); // Email exists
      }
    })
    // Handle any errors during the fetch
    .catch((error) => {
      console.error(error);
      callback(false); // Error occurred, assume email does not exist
    });
}
