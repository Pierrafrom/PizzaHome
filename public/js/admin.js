// Import the CustomAlert module from CustomAlert.js
import { CustomAlert } from "./CustomAlert.js";

window.addEventListener("load", function () {
  if (window.location.hash === "") {
    history.replaceState(null, null, "#stock");
  }
  showTab();
});

// Wait for the DOM to be loaded
document.addEventListener("DOMContentLoaded", function () {
  // Set the active tab based on the URL hash value
  setupTabsListeners();
  // Add an event listener to all quantity input elements with class 'stock-quantity'
  const quantityInputs = document.querySelectorAll(".stock-quantity");
  quantityInputs.forEach((quantityInput) => {
    const initialQuantity = quantityInput.value; // Get the initial quantity
    const confirmButton = quantityInput.nextElementSibling.nextElementSibling; // Select the 'Confirm' button

    // Add an event listener for input changes
    quantityInput.addEventListener("input", () => {
      const newQuantity = quantityInput.value; // Get the new quantity

      // Check if the quantity has changed from its initial state
      if (newQuantity !== initialQuantity) {
        // Show the 'Confirm' button by adding the 'show-flex' class and removing the 'hide' class
        confirmButton.classList.add("show-flex");
        confirmButton.classList.remove("hide");
      } else {
        // Hide the 'Confirm' button by adding the 'hide' class and removing the 'show-flex' class
        confirmButton.classList.add("hide");
        confirmButton.classList.remove("show-flex");
      }
    });
  });

  // Add an event listener to all 'Remove' buttons
  const removeButtons = document.querySelectorAll(".btn-remove");
  removeButtons.forEach((button) => {
    button.addEventListener("click", handleRemoveButtonClick);
  });

  // Add an event listener to all 'Spotlight' checkboxes
  const spotlightsCheckboxes = document.querySelectorAll(".spotlight-checkbox");
  spotlightsCheckboxes.forEach((checkbox) => {
    checkbox.addEventListener("change", handleSpotlightCheckboxChange);
  });

  // Add an event listener to the 'Confirm' button for updating the stock quantity
  const confirmButtons = document.querySelectorAll(".confirm-quantity");
  confirmButtons.forEach((confirmButton) => {
    confirmButton.addEventListener("click", () => {
      // Get the parent <td> element
      const tdElement = confirmButton.parentElement;
      // Find the associated quantity input field
      const quantityInput = tdElement.querySelector(".stock-quantity");

      // Retrieve the data attributes from the quantity input
      const stockId = quantityInput.getAttribute("data-stock-id");

      // Get the new quantity
      const newQuantity = quantityInput.value;

      // Retrieve the data attributes from the quantity input
      const stockType = quantityInput.getAttribute("data-stock-type");

      // Call the updatestockQuantity function with this information
      updatestockQuantity(stockId, stockType, newQuantity);
    });
  });

  // Add an event listener to the 'Cancel' button for updating the stock quantity
  const decrementButtons = document.querySelectorAll(".decrement-button");
  const incrementButtons = document.querySelectorAll(".increment-button");

  // Add an event listener to all 'Remove' buttons
  decrementButtons.forEach((button) => {
    button.addEventListener("click", function () {
      const input = this.nextElementSibling;
      let value = parseInt(input.value, 10);
      value = isNaN(value) ? 0 : value;
      value--;
      input.value = value < input.min ? input.min : value;
      input.dispatchEvent(new Event("input"));
    });
  });

  incrementButtons.forEach((button) => {
    button.addEventListener("click", function () {
      const input = this.previousElementSibling;
      let value = parseInt(input.value, 10);
      value = isNaN(value) ? 0 : value;
      value++;
      input.value = value;
      input.dispatchEvent(new Event("input"));
    });
  });
});

/**
 * Update the quantity of a stock in the stock.
 *
 * @param {string} stockId - The ID of the stock.
 * @param {string} stockType - The type of the stock.
 * @param {string} newQuantity - The new quantity of the stock.
 */
async function updatestockQuantity(stockId, stockType, newQuantity) {
  try {
    const response = await fetch("/api/updatestockQuantity", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        stockId: stockId,
        stockType: stockType,
        newQuantity: newQuantity,
      }),
    });

    // Check the response status
    if (response.ok) {
      const data = await response.json();
      console.log(data);
      if (data.success) {
        // Quantity updated successfully, you can update the stock view here
        location.reload();
      } else {
        console.log(data);
        showMessage("Failed to update stock quantity", CustomAlert.Type.ERROR);
      }
    } else {
      console.log(response);
      showMessage("Failed to update stock quantity", CustomAlert.Type.ERROR);
    }
  } catch (error) {
    showMessage(
      "An error occurred while updating stock quantity",
      CustomAlert.Type.ERROR
    );
  }
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
 * Sets up event listeners for the tabs.
 * When a tab is clicked, it prevents the default action, gets the tab's ID,
 * updates the URL with the tab's ID, and displays the active tab.
 */
function setupTabsListeners() {
  // Get all the tabs
  const tabs = document.querySelectorAll(".tab");

  // For each tab, add a click event listener
  tabs.forEach((tab) => {
    tab.addEventListener("click", (event) => {
      event.preventDefault(); // Empêcher l'action par défaut

      // Supprimer '-tab' de l'ID de l'onglet et mettre à jour l'URL
      const tabIdWithoutTab = tab.id.replace("-tab", "");
      history.replaceState(null, null, `#${tabIdWithoutTab}`);
    });
  });
}

/**
 * Displays the active tab based on the URL hash value.
 */
function showTab() {
  // Récupérer l'ID de l'onglet actif
  const hash = window.location.hash;
  console.log("Hash:", hash); // Débogage: Afficher le hachage

  // Déclarer la variable 'tab'
  const tab = document.querySelector(hash + "-tab");
  if (tab) {
    tab.click();
  }
}

/**
 * Handles the click event of the 'Remove' button for a product.
 *
 * @param {Event} event - The click event.
 */
async function handleRemoveButtonClick(event) {
  const productId = event.target.getAttribute("data-product-id");
  const productType = event.target.getAttribute("data-product-type");
  console.log(productId);
  console.log(productType);

  try {
    const response = await fetch("/api/deleteItem", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        productId: productId,
        productType: productType,
      }),
    });

    // Check the response status
    if (response.ok) {
      const data = await response.json();
      console.log(data);
      if (data.success) {
        // Quantity updated successfully, you can update the stock view here
        location.reload();
      } else {
        console.log(data);
        showMessage("Failed to update stock quantity", CustomAlert.Type.ERROR);
      }
    } else {
      console.log(response);
      showMessage("Failed to update stock quantity", CustomAlert.Type.ERROR);
    }
  } catch (error) {
    showMessage(
      "An error occurred while updating stock quantity",
      CustomAlert.Type.ERROR
    );
  }
}

/**
 * Handles the change event of the 'Spotlight' checkbox for a product.
 *
 * @param {Event} event - The change event.
 */
async function handleSpotlightCheckboxChange(event) {
  const productId = event.target.getAttribute("data-product-id");
  const productType = event.target.getAttribute("data-product-type");

  console.log(productId);
  console.log(productType);

  try {
    const response = await fetch("/api/updateSpotlight", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        productId: productId,
        productType: productType,
      }),
    });

    // Check the response status
    if (response.ok) {
      const data = await response.json();
      console.log(data);
      if (data.success) {
        // Quantity updated successfully, you can update the stock view here
        location.reload();
      } else {
        console.log(data);
        showMessage("Failed to update stock quantity", CustomAlert.Type.ERROR);
      }
    } else {
      console.log(response);
      showMessage("Failed to update stock quantity", CustomAlert.Type.ERROR);
    }
  } catch (error) {
    showMessage(
      "An error occurred while updating stock quantity",
      CustomAlert.Type.ERROR
    );
  }
}
