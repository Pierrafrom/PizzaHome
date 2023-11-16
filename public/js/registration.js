// Import the CustomAlert module from CustomAlert.js
import {CustomAlert} from "./CustomAlert.js";

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
document.addEventListener('DOMContentLoaded', function () {
    // Select all cancel buttons and password toggle icons in the DOM.
    const cancelButtons = document.querySelectorAll('.cancel-btn');
    const toggleIcons = document.querySelectorAll('.toggle-password');
    // Select the login and sign-in forms.
    const loginForm = document.querySelector('.login-form');
    const signinForm = document.querySelector('.signin-form');

    // Add submit event listener for the login form.
    loginForm.addEventListener('submit', function (event) {
        event.preventDefault(); // Prevent the default form submission action.
        if (validateForm(this)) {
            // Validate form fields.
            verifyPassword(this.querySelector('input[name="email"]').value,
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
    signinForm.addEventListener('submit', function (event) {
        event.preventDefault(); // Prevent the default form submission action.
        let isValid = checkPasswords(this) && validatePasswordComplexity(this); // Validate passwords.
        if (validateForm(this) && isValid) {
            this.submit(); // Submit the form if all validations pass.
        } else if (!validateForm(this)) {
            // Show an error message if form validation fails.
            showMessage("Please fill out all fields.", CustomAlert.Type.ERROR);
        }
    });

    // Toggle password visibility when the toggle icon is clicked.
    toggleIcons.forEach(function (icon) {
        icon.addEventListener('click', function () {
            const input = this.closest('.form-group').querySelector('input');

            const isPassword = input.type === 'password';
            input.type = isPassword ? 'text' : 'password';

            const eyeOpenIcon = this.closest('.form-group').querySelector('.eye-open');
            const eyeClosedIcon = this.closest('.form-group').querySelector('.eye-closed');

            if (isPassword) {
                eyeOpenIcon.classList.remove('hide');
                eyeOpenIcon.classList.add('show-flex');
                eyeClosedIcon.classList.add('hide');
                eyeClosedIcon.classList.remove('show-flex');
            } else {
                eyeOpenIcon.classList.add('hide');
                eyeOpenIcon.classList.remove('show-flex');
                eyeClosedIcon.classList.remove('hide');
                eyeClosedIcon.classList.add('show-flex');
            }
        });
    });

    // Add click event listeners for all cancel buttons.
    cancelButtons.forEach(function (cancelButton) {
        cancelButton.addEventListener('click', function (event) {
            event.preventDefault(); // Prevent the default button action.
            // Retrieve the redirect URL value from a hidden input field.
            let redirectUrl = document.querySelector('input[name="redirect"]').value;
            // Redirect to the provided URL or to the home page if no URL is provided.
            window.location.href = redirectUrl ? decodeURIComponent(redirectUrl) : '/';
        });
    });
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
    form.querySelectorAll('input').forEach(input => {
        if (!input.value.trim()) {
            isValid = false;
            input.classList.add('invalid'); // Add 'invalid' class to empty input fields.
        } else {
            input.classList.remove('invalid'); // Remove 'invalid' class if input is filled.
        }
    });
    return isValid;
}

/**
 * Checks if the password and confirm password fields in a form match.
 * If they do not match, an error message is displayed.
 *
 * @param {HTMLFormElement} form - The form containing the password fields.
 * @returns {boolean} True if passwords match, otherwise false.
 */
function checkPasswords(form) {
    const password = form.querySelector('input[name="password"]').value;
    const confirmPassword = form.querySelector('input[name="confirm_password"]').value;
    if (password !== confirmPassword) {
        showMessage("Passwords do not match.", CustomAlert.Type.ERROR); // Show error message if passwords don't match.
        return false;
    }
    return true;
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
    const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;

    if (!passwordRegex.test(password)) {
        showMessage("Password must be at least 8 characters long and include at least one uppercase " +
            "letter, one number, and one special character.", CustomAlert.Type.ERROR); // Show error message if password is not complex enough.
        return false;
    }
    return true;
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
    const data = {email, password};

    fetch('/api/verifyPassword', {
        method: 'POST',
        body: JSON.stringify(data),
        headers: {
            'Content-Type': 'application/json',
            'Accept': '*/*'
        }
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                callback(true);
            } else {
                callback(false);
            }
        })
        .catch(error => {
            console.error(error);
            callback(false);
        });
}


