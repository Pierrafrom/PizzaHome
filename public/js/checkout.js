// Event listener to wait for the DOM to be fully loaded
document.addEventListener("DOMContentLoaded", function () {
  // Select the checkout form and add an event listener for the submit action
  const form = document.querySelector(".checkout-form");
  form.addEventListener("submit", handleFormSubmit);

  // Select the credit card input field and add an event listener for input events
  const cardInput = document.getElementById("card-number");
  if (cardInput) {
    cardInput.addEventListener("input", function (event) {
      formatCardNumber(event.target); // Format the card number as the user types
    });
  }
});

/**
 * Formats the input value of a credit card number.
 *
 * @param {HTMLInputElement} input - The input element containing the card number.
 */
function formatCardNumber(input) {
  // Remove any spaces and limit the input to 16 characters
  let formattedInput = input.value.split(" ").join("");
  if (formattedInput.length > 16) {
    formattedInput = formattedInput.substring(0, 16);
  }

  // Group the digits in sets of four separated by spaces
  if (formattedInput.length > 0) {
    formattedInput = formattedInput.match(new RegExp(".{1,4}", "g")).join(" ");
  }

  // Update the input value with the formatted number
  input.value = formattedInput;
}

/**
 * Fetches geographic coordinates for a given address using an external API.
 *
 * @param {string} address - The address to get coordinates for.
 * @returns {Promise<boolean>} A promise that resolves to true if the address is found, otherwise false.
 */
function fetchCoordinates(address) {
  // Fetch coordinates from the OpenStreetMap API
  return fetch(
    `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(
      address
    )}`
  )
    .then((response) => response.json())
    .then((data) => {
      if (data.length > 0) {
        // If coordinates are found, update input fields with these values
        const coords = data[0];
        document.getElementById("latitude").value = coords.lat;
        document.getElementById("longitude").value = coords.lon;
        return true;
      } else {
        return false;
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      return false;
    });
}

/**
 * Handles the form submission event.
 *
 * @param {Event} event - The event object of the form submission.
 */
function handleFormSubmit(event) {
  event.preventDefault();

  // Gather values from address input fields
  const streetNumber = document.getElementById("street-number").value;
  const street = document.getElementById("street").value;
  const city = document.getElementById("city").value;
  const postalCode = document.getElementById("postal-code").value;

  // Construct the full address
  const fullAddress = `${streetNumber} ${street}, ${city}, ${postalCode}`;

  // Fetch coordinates for the address and handle the form submission accordingly
  fetchCoordinates(fullAddress).then((isAddressFound) => {
    if (isAddressFound) {
      // If the address is found, submit the form
      event.target.submit();
    } else {
      // Otherwise, alert the user about the invalid address
      alert("Address not found. Please enter a valid address.");
    }
  });
}
