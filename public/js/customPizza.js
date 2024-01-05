/**
 * This module handles the logic for custom pizza creation and adding it to the cart.
 * It includes functionality to manage ingredient and supplement selection and
 * sending the custom pizza data to the server.
 */

// Import the CustomAlert module for displaying alert messages
import { CustomAlert } from "./CustomAlert.js";

/**
 * Handles the logic for checking and limiting the number of checkboxes selected.
 * It allows a maximum of 3 checkboxes to be checked at a time.
 */
export function handleCheckboxLogic() {
  const checkboxes = document.querySelectorAll(".ingredient-list .hide");
  let checkedCheckboxes = [];

  checkboxes.forEach((checkbox) => {
    checkbox.addEventListener("change", () => {
      if (checkbox.checked) {
        checkedCheckboxes.push(checkbox);

        if (checkedCheckboxes.length > 3) {
          checkedCheckboxes[0].checked = false;
          checkedCheckboxes.shift();
        }
      } else {
        checkedCheckboxes = checkedCheckboxes.filter(
          (item) => item !== checkbox
        );
      }
    });
  });
}

/**
 * Checks if any of the checkboxes for ingredients or supplements are checked.
 * @returns {boolean} True if any checkbox is checked, false otherwise.
 */
export function isCheckboxChecked() {
  const checkboxes = document.querySelectorAll(
    ".ingredient-list .hide, .supplements input[type='checkbox']"
  );
  return Array.from(checkboxes).some((checkbox) => checkbox.checked);
}

/**
 * Handles the addition of a custom pizza to the cart.
 * @param {HTMLElement} button - The button that triggers the addition to the cart.
 */
export async function handleAddToCartForCustomPizza(button) {
  try {
    const productId = button.getAttribute("data-product-id");

    const productQuantity = getProductQuantity(productId);
    const ingredientsToRemove = getIngredientsToRemove();
    const supplementsToAdd = getSupplementsToAdd();

    const postData = JSON.stringify({
      productId,
      ingredientsToRemove,
      supplementsToAdd,
      productQuantity,
    });

    const data = await sendAddToCartRequest(postData);

    if (data.success) {
      updateCartCount(productQuantity);
      resetButtonState(button);
    } else {
      throw new Error(
        `Failed to add pizza to cart: ${data.message || "Unknown error"}`
      );
    }
  } catch (error) {
    showMessage(error.message, CustomAlert.Type.ERROR);
  }
}

/**
 * Gets the product quantity from the DOM.
 * @param {string} productId - The ID of the product.
 * @returns {number} The quantity of the product.
 */
function getProductQuantity(productId) {
  const productQuantityElement = document.querySelector(
    `.product-quantity[data-product-id="${productId}"]`
  );
  const productQuantity = parseInt(
    productQuantityElement ? productQuantityElement.textContent : 1
  );
  if (isNaN(productQuantity) || productQuantity < 1) {
    throw new Error("Invalid product quantity");
  }
  return productQuantity;
}

/**
 * Retrieves the IDs of ingredients to remove from the pizza.
 * @returns {Array<string>} An array of ingredient IDs to remove.
 */
function getIngredientsToRemove() {
  return Array.from(
    document.querySelectorAll(".ingredient-list .hide:checked")
  ).map((checkbox) => checkbox.id.replace("cross-checkbox-", ""));
}

/**
 * Retrieves the IDs of supplements to add to the pizza.
 * @returns {Array<string>} An array of supplement IDs to add.
 */
function getSupplementsToAdd() {
  return Array.from(
    document.querySelectorAll(".supplements input[type='checkbox']:checked")
  ).map((checkbox) => checkbox.value);
}

/**
 * Sends a request to the server to add a custom pizza to the cart.
 * @param {string} postData - The data to be sent in the request.
 * @returns {Promise<Object>} A promise that resolves to the response data.
 */
async function sendAddToCartRequest(postData) {
  const response = await fetch("/api/addCustomPizzaToCart", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: postData,
  });

  if (!response.ok) {
    throw new Error(
      `Server returned ${response.status} : ${response.statusText}`
    );
  }

  return response.json();
}

/**
 * Displays a custom alert message.
 * @param {string} message - The message to be displayed.
 * @param {CustomAlert.Type} type - The type of alert (e.g., error, info).
 */
function showMessage(message, type) {
  const alert = new CustomAlert();
  alert.show(message, type);
}

/**
 * Updates the cart count in the DOM.
 * @param {number} quantity - The quantity to add to the cart count.
 */
function updateCartCount(quantity) {
  const cartCountElement = document.querySelector(".cart-count");
  const cartCount = parseInt(cartCountElement.textContent);
  cartCountElement.textContent = cartCount + quantity;
}

/**
 * Resets the state of the add-to-cart button after adding a product.
 * @param {HTMLElement} button - The button that triggers the addition to the cart.
 */
function resetButtonState(button) {
  const cartPlusIcon = button.querySelector(".cart-plus");
  const cartCheckIcon = button.querySelector(".cart-check");
  const productQuantityElement =
    button.parentElement.querySelector(".product-quantity");

  cartPlusIcon.classList.remove("show-flex");
  cartPlusIcon.classList.add("hide");
  cartCheckIcon.classList.remove("hide");
  cartCheckIcon.classList.add("show-flex");
  productQuantityElement.textContent = 0;

  setTimeout(() => {
    cartPlusIcon.classList.remove("hide");
    cartPlusIcon.classList.add("show-flex");
    cartCheckIcon.classList.remove("show-flex");
    cartCheckIcon.classList.add("hide");
  }, 2000);
}
