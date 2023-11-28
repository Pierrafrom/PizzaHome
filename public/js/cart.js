// wait for the DOM to be loaded

document.addEventListener("DOMContentLoaded", function (event) {
    const removeCartItemButtons = document.getElementsByClassName('cart-remove');

    removeCartItemButtons.forEach(button => {
        button.addEventListener('click', function () {
            const productId = event.target.getAttribute('data-product-id');
            const productType = event.target.getAttribute('data-product-type');

            if (productId && productType) {
                removeCartItem(productId, productType);
            }
        });
    });
});

function removeCartItem(productId, productType) {

    const url = `/cart/${productType}/${productId}`;
    const options = {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json'
        }
    };

    fetch(url, options)
        .then(response => {
            if (response.ok) {
                return response.json();
            }
            throw new Error('Network response was not ok.');
        })
        .then(data => {
            if (data.success) {
                const cartItem = document.querySelector(`.cart-item[data-product-id="${productId}"]`);
                cartItem.remove();
                updateCartTotal();
            }
        })
        .catch(error => console.log(error.message));

}