// Wait for the DOM to be ready
document.addEventListener("DOMContentLoaded", function () {
  // Get all "add to cart" buttons
  const quantityButtons = document.querySelectorAll(".btn-quantity");

  quantityButtons.forEach((button) => {
    button.addEventListener("click", function () {
      const productId = this.parentElement
        .querySelector(".product-quantity")
        .getAttribute("data-product-id");
      const quantitySpan =
        this.parentElement.querySelector(".product-quantity");
      const currentQuantity = parseInt(quantitySpan.textContent);

      if (this.classList.contains("btn-plus")) {
        quantitySpan.textContent = (currentQuantity + 1).toString();
      } else if (this.classList.contains("btn-minus") && currentQuantity > 0) {
        quantitySpan.textContent = (currentQuantity - 1).toString();
      }
    });
  });
});
