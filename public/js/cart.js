// Wait for the DOM to be ready
document.addEventListener('DOMContentLoaded', function () {
    // Get all "add to cart" buttons
    const addToCartButtons = document.querySelectorAll('.add-to-cart');

    // Add a click event listener to each button
    addToCartButtons.forEach(function (button) {
        button.addEventListener('click', function() {
            let productType = button.getAttribute('data-product-type');
            let productId = button.getAttribute('data-product-id');
            addToCart(productType, productId);
        });
    });
});

function addToCart(productType, productId) {
    let postData = 'productId=' + encodeURIComponent(productId) + '&productType=' + encodeURIComponent(productType);

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
