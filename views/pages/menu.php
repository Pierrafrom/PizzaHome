<?php
$title = "Menu";
$cssFiles = ["banner.css", "menu.css"];
$scriptFile = "menu.js";
?>

<div class="banner classic-banner">
    <img src="/img/secondary-banner.webp" alt="Photo of parasols on tables at the café on an old street.">
    <h1>Menu</h1>
</div>

<ul class="tabs">
    <li><a href="#pizza-tab" class="tab active-tab">Pizza</a></li>
    <li><a href="#drink-tab" class="tab">Drink</a></li>
    <li><a href="#dessert-tab" class="tab">Dessert</a></li>
</ul>

<section id="pizza-tab" class="tab-content show-flex">
    <h2>Pizza</h2>

    <div class="pizza-item">
        <div>
            <h3>Marinara</h3>
            <p><i>Tomato sauce, garlic, oregano, olive oil (no cheese).</i></p>
            <p><strong>€9.99</strong></p>
        </div>
        <button class="btn-primary">Add to Cart</button>
    </div>

    <div class="pizza-item">
        <div>
            <h3>Margherita</h3>
            <p><i>Tomato sauce, mozzarella, fresh basil, olive oil.</i></p>
            <p><strong>€10.99</strong></p>
        </div>
        <button class="btn-primary">Add to Cart</button>
    </div>

    <div class="pizza-item">
        <div>
            <h3>Pepperoni</h3>
            <p><i>Tomato sauce, mozzarella, pepperoni (spicy salami).</i></p>
            <p><strong>€12.99</strong></p>
        </div>
        <button class="btn-primary">Add to Cart</button>
    </div>

    <div class="pizza-item">
        <div>
            <h3>Quattro Formaggi (Four Cheese)</h3>
            <p><i>Mozzarella, gorgonzola, parmesan, goat cheese.</i></p>
            <p><strong>€13.99</strong></p>
        </div>
        <button class="btn-primary">Add to Cart</button>
    </div>

    <div class="pizza-item">
        <div>
            <h3>Hawaiian</h3>
            <p><i>Tomato sauce, mozzarella, ham, pineapple.</i></p>
            <p><strong>€13.99</strong></p>
        </div>
        <button class="btn-primary">Add to Cart</button>
    </div>

    <div class="pizza-item">
        <div>
            <h3>Diavola</h3>
            <p><i>Tomato sauce, mozzarella, pepperoni, red peppers.</i></p>
            <p><strong>€13.99</strong></p>
        </div>
        <button class="btn-primary">Add to Cart</button>
    </div>

    <div class="pizza-item">
        <div>
            <h3>Vegetarian</h3>
            <p><i>Tomato sauce, mozzarella, mushrooms, bell peppers, onions, black olives, tomatoes.</i></p>
            <p><strong>€14.99</strong></p>
        </div>
        <button class="btn-primary">Add to Cart</button>
    </div>

    <div class="pizza-item">
        <div>
            <h3>Calzone</h3>
            <p><i>Tomato sauce, mozzarella, ham, mushrooms (folded in the shape of a turnover).</i></p>
            <p><strong>€14.99</strong></p>
        </div>
        <button class="btn-primary">Add to Cart</button>
    </div>

    <div class="pizza-item">
        <div>
            <h3>Prosciutto e Funghi</h3>
            <p><i>Tomato sauce, mozzarella, Parma ham, mushrooms.</i></p>
            <p><strong>€14.99</strong></p>
        </div>
        <button class="btn-primary">Add to Cart</button>
    </div>

    <div class="pizza-item">
        <div>
            <h3>Quattro Stagioni (Four Seasons)</h3>
            <p><i>Tomato sauce, mozzarella, ham, artichokes, black olives, mushrooms
                    (divided into four sections with each ingredient representing a season).</i></p>
            <p><strong>€15.99</strong></p>
        </div>
        <button class="btn-primary">Add to Cart</button>
    </div>

</section>

<section id="drink-tab" class="tab-content hide">
    <h2>Drink</h2>
</section>

<section id="dessert-tab" class="tab-content hide">
    <h2>Dessert</h2>
</section>