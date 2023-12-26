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
    <h1>Admin</h1>
</section>

<ul class="tabs" role="tablist">
    <li><a href="#graphics-tab" class="tab active-tab" role="tab" aria-controls="graphics-tab" aria-selected="true"
           id="tab1">Graphics</a>
    </li>
    <li><a href="#stock-tab" class="tab" role="tab" aria-controls="stock-tab" aria-selected="false"
           id="tab2">Stocks</a>
    </li>
    <li><a href="#menu-tab" class="tab" role="tab" aria-controls="menu-tab" aria-selected="false"
           id="tab3">Menu</a>
    </li>
</ul>

<section id="graphics-tab" class="tab-content show-flex">
    <div class="container">
        <h2>Graphics</h2>


    </div>
</section>

<section id="stock-tab" class="tab-content hide">
    <h2>Stocks</h2>
    <div class="container">
        <h3>Ingredients</h3>

        <?php
        if (isset($this->viewData['ingredientSection'])) {
            echo $this->viewData['ingredientSection'];
        }
        ?>

    </div>
    <div class="container">
        <h3>Wines</h3>
        <?php
        if (isset($this->viewData['winesSection'])) {
            echo $this->viewData['winesSection'];
        }
        ?>
    </div>
    <div class="container">
        <h3>Sodas</h3>
        <?php
        if (isset($this->viewData['sodasSection'])) {
            echo $this->viewData['sodasSection'];
        }
        ?>
    </div>
</section>

<section id="menu-tab" class="tab-content hide">
    <div class="container">
        <h2>Menu</h2>


    </div>
</section>