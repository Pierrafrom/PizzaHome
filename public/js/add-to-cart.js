// Import the CustomAlert module from CustomAlert.js
import {CustomAlert} from "./CustomAlert.js";

// Wait for the DOM to be ready
document.addEventListener('DOMContentLoaded', function () {
    // Get all "add to cart" buttons
    const addToCartButtons = document.querySelectorAll('.add-to-cart');

    // Add a click event listener to each button
    addToCartButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            let productType = button.getAttribute('data-product-type');
            let productId = button.getAttribute('data-product-id');

            // get the product quantity
            let productQuantityElement = document.querySelector('.product-quantity[data-product-id="'
                + productId + '"]');
            let productQuantity = parseInt(productQuantityElement.textContent);

            // check if the product quantity is valid
            if (isNaN(productQuantity) || productQuantity < 1) {
                showMessage('Invalid product quantity.', CustomAlert.Type.ERROR);
                return;
            }

            addToCart(productType, productId, productQuantity);
        });
    });
});

function addToCart(productType, productId, productQuantity) {
    let postData = 'productId=' + encodeURIComponent(productId)
        + '&productType=' + encodeURIComponent(productType)
        + '&productQuantity=' + encodeURIComponent(productQuantity);

    fetch('/api/addProductToCart', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: postData
    })
        .then(response => {
            if (!response.ok) {
                throw new Error('Server returned ' + response.status + ' : ' + response.statusText);
            }
            return response.json();
        })
        .then(data => {
            console.log(data);
        })
        .catch(error => {
            console.error('Error:', error);
        });
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
