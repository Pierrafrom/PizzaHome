<section class="banner classic-banner">
    <picture>
        <!-- WebP Format -->
        <source media="(max-width: 1000px)" srcset="/img/banners/secondary-banner-sm-min.webp, 
                /img/banners/secondary-banner-l-min.webp 2x, 
                /img/banners/secondary-banner-xl-min.webp 3x" type="image/webp">
        <source media="(min-width: 1001px) and (max-width: 1500px)" srcset="/img/banners/secondary-banner-m-min.webp, 
                /img/banners/secondary-banner-xl-min.webp 2x" type="image/webp">
        <source media="(min-width: 1501px) and (max-width: 2000px)" srcset="/img/banners/secondary-banner-l-min.webp, 
                /img/banners/secondary-banner-xl-min.webp 2x" type="image/webp">
        <source media="(min-width: 2001px)" srcset="/img/banners/secondary-banner-xl-min.webp" type="image/webp">

        <!-- PNG Format -->
        <source media="(max-width: 1000px)" srcset="/img/banners/secondary-banner-sm-min.png, 
                /img/banners/secondary-banner-l-min.png 2x, 
                /img/banners/secondary-banner-xl-min.png 3x" type="image/png">
        <source media="(min-width: 1001px) and (max-width: 1500px)" srcset="/img/banners/secondary-banner-m-min.png, 
                /img/banners/secondary-banner-xl-min.png 2x" type="image/png">
        <source media="(min-width: 1501px) and (max-width: 2000px)" srcset="/img/banners/secondary-banner-l-min.png, 
                /img/banners/secondary-banner-xl-min.png 2x" type="image/png">
        <source media="(min-width: 2001px)  " srcset="/img/banners/secondary-banner-xl-min.png" type="image/png">

        <!-- Fallback -->
        <img src="/img/banners/secondary-banner-sm-min.png" alt="Photo of parasols on tables at the café on an old street." decoding="async" loading="lazy">
    </picture>
    <h1>Admin</h1>
</section>

<ul class="tabs" role="tablist">
    <li><a href="#graphics-section" class="tab" role="tab" aria-controls="graphics-section" aria-selected="true" id="graphics-tab">Graphics</a>
    </li>
    <li><a href="#stock-section" class="tab" role="tab" aria-controls="stock-section" aria-selected="false" id="stock-tab">Stocks</a>
    </li>
    <li><a href="#menu-section" class="tab" role="tab" aria-controls="menu-section" aria-selected="false" id="menu-tab">Menu</a>
    </li>
</ul>

<section id="graphics-section" class="tab-content hide">
    <h2>Graphics</h2>
    <div class="container">
        <h3>Sales</h3>
        <div id="sales-chart"></div>
    </div>
    <div class="container">
        <h3>Pizza stats</h3>
        <div id="pizza-chart"></div>
    </div>
    <div class="container">
        <h3>Products</h3>
        <div class="cheese-chart-container">
            <div class="item" id="pizza"></div>
            <div class="item" id="dessert"></div>
            <div class="item" id="wine"></div>
            <div class="item" id="cocktail"></div>
            <div class="item" id="soda"></div>
        </div>
    </div>
</section>

<section id="stock-section" class="tab-content hide">
    <h2>Stocks</h2>
    <div class="container">
        <h3>Ingredients<span>
                <a href="/creation?class=App%5Cmodels%5CIngredient&redirect=<?= urlencode($_SERVER['REQUEST_URI']) ?>" class="btn-primary">
                    New Ingredient
                </a>
            </span></h3>

        <?php
        if (isset($this->viewData['ingredientSection'])) {
            echo $this->viewData['ingredientSection'];
        }
        ?>

    </div>
    <div class="container">
        <h3>Wines<span>
                <a href="/creation?class=App%5Cmodels%5CWine&redirect=<?= urlencode($_SERVER['REQUEST_URI']) ?>" class="btn-primary">
                    New Wine</a>
            </span></h3>
        <?php
        if (isset($this->viewData['winesSection'])) {
            echo $this->viewData['winesSection'];
        }
        ?>
    </div>
    <div class="container">
        <h3>Sodas<span>
                <a href="/creation?class=App%5Cmodels%5CSoda&redirect=<?= urlencode($_SERVER['REQUEST_URI']) ?>" class="btn-primary">
                    New Soda</a>
            </span></h3>
        <?php
        if (isset($this->viewData['sodasSection'])) {
            echo $this->viewData['sodasSection'];
        }
        ?>
    </div>
</section>

<section id="menu-section" class="tab-content hide">
    <h2>Menu</h2>
    <div class="container">
        <h3>Pizzas<span>
                <a href="/creation?class=App%5Cmodels%5CPizza&redirect=<?= urlencode($_SERVER['REQUEST_URI']) ?>" class="btn-primary">
                    New Pizza</a>
            </span></h3>
        <?php
        if (isset($this->viewData['pizzasSection'])) {
            echo $this->viewData['pizzasSection'];
        }
        ?>
    </div>
    <div class="container">
        <h3>Desserts<span>
                <a href="/creation?class=App%5Cmodels%5CDessert&redirect=<?= urlencode($_SERVER['REQUEST_URI']) ?>" class="btn-primary">
                    New Dessert</a>
            </span></h3>
        <?php
        if (isset($this->viewData['dessertsSection'])) {
            echo $this->viewData['dessertsSection'];
        }
        ?>
    </div>
    <div class="container">
        <h3>Cocktails<span>
                <a href="/creation?class=App%5Cmodels%5CCocktail&redirect=<?= urlencode($_SERVER['REQUEST_URI']) ?>" class="btn-primary">
                    New Cocktail</a>
            </span></h3>
        <?php
        if (isset($this->viewData['cocktailsSection'])) {
            echo $this->viewData['cocktailsSection'];
        }
        ?>
    </div>
</section>