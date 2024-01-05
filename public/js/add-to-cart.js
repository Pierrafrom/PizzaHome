// Import the CustomAlert module from CustomAlert.js
import { CustomAlert } from "./CustomAlert.js";

document.addEventListener("DOMContentLoaded", function () {
  if (shouldImportCustomPizza()) {
    import("./customPizza.js").then((customPizzaModule) => {
      customPizzaModule.handleCheckboxLogic();
    });
  }

  const addToCartButtons = document.querySelectorAll(".add-to-cart");
  addToCartButtons.forEach(function (button) {
    button.addEventListener("click", function () {
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

function handleAddToCartForStandardProduct(button) {
  let productId = button.getAttribute("data-product-id");
  let productType = button.getAttribute("data-product-type");

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

  if (isNaN(productQuantity) || productQuantity < 1) {
    showMessage("Invalid product quantity.", CustomAlert.Type.ERROR);
    return;
  }

  addToCart(productType, productId, productQuantity)
    .then(() => {
      updateCartCount(productQuantity);
      resetButtonState(button);
    })
    .catch((error) => console.error("Error:", error));
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

function updateCartCount(quantity) {
  let cartCountElement = document.querySelector(".cart-count");
  let cartCount = parseInt(cartCountElement.textContent);
  cartCount += quantity;
  cartCountElement.textContent = cartCount;
}

async function addToCart(productType, productId, productQuantity) {
  let postData =
    "productId=" +
    encodeURIComponent(productId) +
    "&productType=" +
    encodeURIComponent(productType) +
    "&productQuantity=" +
    encodeURIComponent(productQuantity);

  try {
    let response = await fetch("/api/addProductToCart", {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: postData,
    });

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

function shouldImportCustomPizza() {
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
  const alert = new CustomAlert();
  alert.show(message, type); // Show the custom alert with the provided message and type.
}
