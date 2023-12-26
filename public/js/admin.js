// Import the CustomAlert module from CustomAlert.js
import {CustomAlert} from "./CustomAlert.js";

// Wait for the DOM to be loaded
document.addEventListener("DOMContentLoaded", function () {

    // Add an event listener to all quantity input elements with class 'stock-quantity'
    const quantityInputs = document.querySelectorAll(".stock-quantity");
    quantityInputs.forEach((quantityInput) => {
        const initialQuantity = quantityInput.value; // Get the initial quantity
        const confirmButton = quantityInput.nextElementSibling.nextElementSibling; // Select the 'Confirm' button

        console.log(quantityInput);

        // Add an event listener for input changes
        quantityInput.addEventListener("input", () => {
            const newQuantity = quantityInput.value; // Get the new quantity

            console.log(newQuantity);

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
            s
            const newQuantity = quantityInput.value;

            // Now you have stockId, stockType, and newQuantity for the stock
            // Call the updatestockQuantity function with this information
            updatestockQuantity(stockId, stockType, newQuantity);
        });
    });

    const decrementButtons = document.querySelectorAll('.decrement-button');
    const incrementButtons = document.querySelectorAll('.increment-button');

    decrementButtons.forEach(button => {
        button.addEventListener('click', function () {
            const input = this.nextElementSibling;
            let value = parseInt(input.value, 10);
            value = isNaN(value) ? 0 : value;
            value--;
            input.value = value < input.min ? input.min : value;
            input.dispatchEvent(new Event('input'));
        });
    });

    incrementButtons.forEach(button => {
        button.addEventListener('click', function () {
            const input = this.previousElementSibling;
            let value = parseInt(input.value, 10);
            value = isNaN(value) ? 0 : value;
            value++;
            input.value = value;
            input.dispatchEvent(new Event('input'));
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
            if (data.success) {
                // Quantity updated successfully, you can update the stock view here
                location.reload();
            } else {
                showMessage("Failed to update stock quantity", CustomAlert.Type.ERROR);
            }
        } else {
            showMessage("Failed to update stock quantity", CustomAlert.Type.ERROR);
        }
    } catch (error) {
        showMessage("An error occurred while updating stock quantity", CustomAlert.Type.ERROR);
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


