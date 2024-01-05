// Import the CustomAlert module for displaying alerts
import { CustomAlert } from "./CustomAlert.js";

// Event listener for the DOMContentLoaded event to ensure the DOM is fully loaded
document.addEventListener("DOMContentLoaded", function () {
  // Conditionally import and handle logic for custom pizzas
  if (shouldImportCustomPizza()) {
    import("./customPizza.js").then((customPizzaModule) => {
      customPizzaModule.handleCheckboxLogic();
    });
  }

  // Select all 'add-to-cart' buttons and attach click event listeners
  const addToCartButtons = document.querySelectorAll(".add-to-cart");
  addToCartButtons.forEach(function (button) {
    button.addEventListener("click", function () {
      // Conditional logic to handle adding custom pizzas or standard products to the cart
      if (shouldImportCustomPizza()) {
        import("./customPizza.js").then((customPizzaModule) => {
          if (customPizzaModule.isCheckboxChecked()) {
            try {
              customPizzaModule.handleAddToCartForCustomPizza(button);
            } catch (e) {
              console.log(e);
            }
          } else {
            try {
              handleAddToCartForStandardProduct(button);
            } catch (e) {
              console.log(e);
            }
          }
        });
      } else {
        try {
          handleAddToCartForStandardProduct(button);
        } catch (e) {
          console.log(e);
        }
      }
    });
  });
});

/**
 * Handles the addition of a standard product to the shopping cart.
 *
 * @param {HTMLElement} button - The button element that triggered the add-to-cart action.
 */
function handleAddToCartForStandardProduct(button) {
  // Retrieve product information from the button's data attributes
  let productId = button.getAttribute("data-product-id");
  let productType = button.getAttribute("data-product-type");

  // Find the corresponding quantity element and parse its value
  let productQuantityElement = document.querySelector(
    '.product-quantity[data-product-id="' +
      productId +
      '"][data-product-type="' +
      productType +
      '"]'
  );
  let productQuantity = productQuantityElement
    ? parseInt(productQuantityElement.textContent)
    : 1;

  // Validate product quantity and show error message if invalid
  if (isNaN(productQuantity) || productQuantity < 1) {
    showMessage("Invalid product quantity.", CustomAlert.Type.ERROR);
    return;
  }

  // Add the product to the cart and handle the response
  addToCart(productType, productId, productQuantity)
    .then(() => {
      updateCartCount(productQuantity);
      resetButtonState(button);
    })
    .catch((error) => console.error("Error:", error));
}

/**
 * Resets the state of the add-to-cart button after a successful action.
 *
 * @param {HTMLElement} button - The button element to reset.
 */
function resetButtonState(button) {
  // Switch icons to indicate the product has been added
  const cartPlusIcon = button.querySelector(".cart-plus");
  const cartCheckIcon = button.querySelector(".cart-check");
  const productQuantityElement =
    button.parentElement.querySelector(".product-quantity");

  // Update button and quantity display
  cartPlusIcon.classList.remove("show-flex");
  cartPlusIcon.classList.add("hide");
  cartCheckIcon.classList.remove("hide");
  cartCheckIcon.classList.add("show-flex");
  productQuantityElement.textContent = 0;

  // Revert button state after a delay
  setTimeout(() => {
    cartPlusIcon.classList.remove("hide");
    cartPlusIcon.classList.add("show-flex");
    cartCheckIcon.classList.remove("show-flex");
    cartCheckIcon.classList.add("hide");
  }, 2000);
}

/**
 * Updates the shopping cart item count displayed on the page.
 *
 * @param {number} quantity - The quantity to add to the cart count.
 */
function updateCartCount(quantity) {
  // Update the cart count displayed in the UI
  let cartCountElement = document.querySelector(".cart-count");
  let cartCount = parseInt(cartCountElement.textContent);
  cartCount += quantity;
  cartCountElement.textContent = cartCount;
}

/**
 * Asynchronously adds a product to the shopping cart.
 *
 * @param {string} productType - The type of the product (e.g., pizza, soda).
 * @param {string} productId - The ID of the product.
 * @param {number} productQuantity - The quantity of the product to add.
 * @returns {Promise} A promise that resolves with the server response.
 */
async function addToCart(productType, productId, productQuantity) {
  // Prepare data for POST request
  let postData =
    "productId=" +
    encodeURIComponent(productId) +
    "&productType=" +
    encodeURIComponent(productType) +
    "&productQuantity=" +
    encodeURIComponent(productQuantity);

  // Send POST request to the server and handle the response
  try {
    let response = await fetch("/api/addProductToCart", {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: postData,
    });

    // Throw an error if the response is not OK
    if (!response.ok) {
      throw new Error(
        "Server returned " + response.status + " : " + response.statusText
      );
    }

    return await response.json();
  } catch (error) {
    throw error;
  }
}

/**
 * Determines if the custom pizza script should be imported based on the current URL.
 *
 * @returns {boolean} True if the custom pizza script should be imported, false otherwise.
 */
function shouldImportCustomPizza() {
  // Parse the current URL to determine if the custom pizza script is needed
  const url = new URL(window.location.href);
  const id = url.searchParams.get("id");
  const type = url.searchParams.get("type");

  return type === "pizza" && !isNaN(Number(id));
}

/**
 * Displays a custom alert message with a specified type.
 *
 * @param {string} message - The message to be displayed in the alert.
 * @param {CustomAlert.Type} type - The type of the alert (e.g., error, info).
 */
function showMessage(message, type) {
  // Create a new CustomAlert instance and show the message
  const alert = new CustomAlert();
  alert.show(message, type); // Show the custom alert with the provided message and type.
}
