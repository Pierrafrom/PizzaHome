// Import the CustomAlert module from CustomAlert.js
import { CustomAlert } from "./CustomAlert.js";

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

export function isCheckboxChecked() {
  const checkboxes = document.querySelectorAll(
    ".ingredient-list .hide, .supplements input[type='checkbox']"
  );
  return Array.from(checkboxes).some((checkbox) => checkbox.checked);
}

export async function handleAddToCartForCustomPizza(button) {
  try {
    const productId = button.getAttribute("data-product-id");
    console.log(`Adding custom pizza to cart, Product ID: ${productId}`);

    const productQuantity = getProductQuantity(productId);
    const ingredientsToRemove = getIngredientsToRemove();
    const supplementsToAdd = getSupplementsToAdd();

    console.log(`Product Quantity: ${productQuantity}`);
    console.log(`Ingredients to Remove: ${ingredientsToRemove.join(", ")}`);
    console.log(`Supplements to Add: ${supplementsToAdd.join(", ")}`);

    const postData = JSON.stringify({
      productId,
      ingredientsToRemove,
      supplementsToAdd,
      productQuantity,
    });

    const data = await sendAddToCartRequest(postData);

    if (data.success) {
      console.log("Pizza custom added to cart successfully");
      updateCartCount(productQuantity);
      resetButtonState(button);
    } else {
      console.error(
        `Failed to add pizza to cart. Error: ${data.message || "Unknown error"}`
      );
    }
  } catch (error) {
    console.error("Error in handleAddToCartForCustomPizza:", error);
    showMessage(error.message, CustomAlert.Type.ERROR);
  }
}

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

function getIngredientsToRemove() {
  return Array.from(
    document.querySelectorAll(".ingredient-list .hide:checked")
  ).map((checkbox) => checkbox.id.replace("cross-checkbox-", ""));
}

function getSupplementsToAdd() {
  return Array.from(
    document.querySelectorAll(".supplements input[type='checkbox']:checked")
  ).map((checkbox) => checkbox.value);
}

async function sendAddToCartRequest(postData) {
  try {
    const response = await fetch("/api/addCustomPizzaToCart", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: postData,
    });

    if (!response.ok) {
      console.error("Response status:", response.status);
      console.error("Response status text:", response.statusText);
      throw new Error(
        "Server returned " + response.status + " : " + response.statusText
      );
    }

    return await response.json();
  } catch (error) {
    console.error("Fetch error:", error);
    throw error;
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

function updateCartCount(quantity) {
  let cartCountElement = document.querySelector(".cart-count");
  let cartCount = parseInt(cartCountElement.textContent);
  cartCount += quantity;
  cartCountElement.textContent = cartCount;
}

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
