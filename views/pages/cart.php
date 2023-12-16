<section class="banner classic-banner">
    <picture>
        <!-- WebP Format -->
        <source media="(max-width: 1000px)"
                srcset="/img/banners/secondary-banner-sm-min.webp,
                /img/banners/secondary-banner-l-min.webp 2x,
                /img/banners/secondary-banner-xl-min.webp 3x"
                type="image/webp">
        <source media="(min-width: 1001px) and (max-width: 1500px)"
                srcset="/img/banners/secondary-banner-m-min.webp,
                /img/banners/secondary-banner-xl-min.webp 2x"
                type="image/webp">
        <source media="(min-width: 1501px) and (max-width: 2000px)"
                srcset="/img/banners/secondary-banner-l-min.webp,
                /img/banners/secondary-banner-xl-min.webp 2x"
                type="image/webp">
        <source media="(min-width: 2001px)"
                srcset="/img/banners/secondary-banner-xl-min.webp"
                type="image/webp">

        <!-- PNG Format -->
        <source media="(max-width: 1000px)"
                srcset="/img/banners/secondary-banner-sm-min.png,
                /img/banners/secondary-banner-l-min.png 2x,
                /img/banners/secondary-banner-xl-min.png 3x"
                type="image/png">
        <source media="(min-width: 1001px) and (max-width: 1500px)"
                srcset="/img/banners/secondary-banner-m-min.png,
                /img/banners/secondary-banner-xl-min.png 2x"
                type="image/png">
        <source media="(min-width: 1501px) and (max-width: 2000px)"
                srcset="/img/banners/secondary-banner-l-min.png,
                /img/banners/secondary-banner-xl-min.png 2x"
                type="image/png">
        <source media="(min-width: 2001px)  "
                srcset="/img/banners/secondary-banner-xl-min.png"
                type="image/png">

        <!-- Fallback -->
        <img src="/img/banners/secondary-banner-sm-min.png"
             alt="Photo of parasols on tables at the café on an old street."
             decoding="async"
             loading="lazy"
        >
    </picture>
    <h1>Cart</h1>
</section>

<section>
    <div class="container">
        <h2>Order Summary</h2>
        <?php
        $cart = $this->viewData['cart'];
        if (isset($cart)) {
            echo $cart;
        } else {
            echo '<p>The cart is empty</p>';
        }
        ?>
        <a href="/menu" class="small-link continue-shopping">Continue shopping &#8594;</a>

        <h2>Total</h2>
        <div>
            <table class="total-table">
                <tr>
                    <th>Delivery</th>
                    <td>Free</td>
                </tr>
                <tr>
                    <th>Total</th>
                    <td><?php echo $this->viewData['totalPrice']; ?>€</td>
                </tr>
            </table>
            <a href="/checkout" class="btn btn-primary checkout">Checkout</a>
        </div>

    </div>
</section>