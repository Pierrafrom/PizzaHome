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
    <li><a href="#graphics-section" class="tab" role="tab" aria-controls="graphics-section" aria-selected="true"
           id="graphics-tab">Graphics</a>
    </li>
    <li><a href="#stock-section" class="tab" role="tab" aria-controls="stock-section" aria-selected="false"
           id="stock-tab">Stocks</a>
    </li>
    <li><a href="#menu-section" class="tab" role="tab" aria-controls="menu-section" aria-selected="false"
           id="menu-tab">Menu</a>
    </li>
</ul>

<section id="graphics-section" class="tab-content hide">
    <div class="container">
        <h2>Graphics</h2>


    </div>
</section>

<section id="stock-section" class="tab-content hide">
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

<section id="menu-section" class="tab-content hide">
    <div class="container">
        <h2>Menu</h2>


    </div>
</section>