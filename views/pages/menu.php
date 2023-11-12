<?php

use App\models\Pizza;

$title = "Menu";
$cssFiles = ["banner.css", "menu.css"];
$scriptFile = "tabs.js";
?>

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
    <h1>Menu</h1>
</section>

<ul class="tabs" role="tablist">
    <li><a href="#pizza-tab" class="tab active-tab" role="tab" aria-controls="pizza-tab" aria-selected="true"
           id="tab1">Pizza</a>
    </li>
    <li><a href="#drink-tab" class="tab" role="tab" aria-controls="drink-tab" aria-selected="false"
           id="tab2">Drink</a>
    </li>
    <li><a href="#dessert-tab" class="tab" role="tab" aria-controls="dessert-tab" aria-selected="false"
           id="tab3">Dessert</a>
    </li>
</ul>

<section id="pizza-tab" class="tab-content show-flex">
    <div class="container">
        <h2>Pizza</h2>

        <?php

        try {
            $pizzas = Pizza::getAllPizzas();

            foreach ($pizzas as $pizza) {
                echo '<div class="tab-item">';
                echo '<article>';
                echo $pizza;
                echo '</article>';
                echo '<button class="btn-primary">Add to Cart</button>';
                echo '</div>';
            }

        } catch (Exception $e) {
            echo '<p>An error occurred while retrieving pizzas.</p>';
        }

        ?>
    </div>
</section>

<section id="drink-tab" class="tab-content hide">
    <h2>Drink</h2>
</section>

<section id="dessert-tab" class="tab-content hide">
    <h2>Dessert</h2>
</section>