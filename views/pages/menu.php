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
        if (isset($this->viewData['pizzaSection'])) {
            echo $this->viewData['pizzaSection'];
        }
        ?>
    </div>
</section>

<section id="drink-tab" class="tab-content hide">
    <div class="container">
        <h2>Drink</h2>
        <div class="icon-title-container">
            <img src="/img/icons/soda-icon.svg" alt="Soda icon">
        </div>
        <?php
        if (isset($this->viewData['sodaSection'])) {
            echo $this->viewData['sodaSection'];
        }
        ?>

        <div class="icon-title-container">
            <img src="/img/icons/wine-icon.svg" alt="Wine icon">
        </div>

        <div class="tab-item"><h3><i>White</i></h3></div>
        <?php
        if (isset($this->viewData['whiteWineSection'])) {
            echo $this->viewData['whiteWineSection'];
        }
        ?>

        <div class="tab-item"><h3><i>Red</i></h3></div>

        <?php
        if (isset($this->viewData['redWineSection'])) {
            echo $this->viewData['redWineSection'];
        }
        ?>

        <div class="icon-title-container">
            <img src="/img/icons/cocktail-icon.svg" alt="Cocktail icon">
        </div>

        <?php
        if (isset($this->viewData['cocktailSection'])) {
            echo $this->viewData['cocktailSection'];
        }
        ?>

    </div>
</section>

<section id="dessert-tab" class="tab-content hide">
    <div class="container">
        <h2>Dessert</h2>
        <?php
        if (isset($this->viewData['dessertSection'])) {
            echo $this->viewData['dessertSection'];
        }
        ?>
    </div>
</section>