<section class="banner home-banner">
    <picture>
        <!-- WebP Format -->
        <source media="(max-width: 1000px)" srcset="/img/banners/home-banner-sm-min.webp, 
                /img/banners/home-banner-l-min.webp 2x, 
                /img/banners/home-banner-xl-min.webp 3x" type="image/webp">
        <source media="(min-width: 1001px) and (max-width: 1500px)" srcset="/img/banners/home-banner-m-min.webp, 
                /img/banners/home-banner-xl-min.webp 2x" type="image/webp">
        <source media="(min-width: 1501px) and (max-width: 2000px)" srcset="/img/banners/home-banner-l-min.webp, 
                /img/banners/home-banner-xl-min.webp 2x" type="image/webp">
        <source media="(min-width: 2001px)" srcset="/img/banners/home-banner-xl-min.webp" type="image/webp">

        <!-- PNG Format -->
        <source media="(max-width: 1000px)" srcset="/img/banners/home-banner-sm-min.png, 
                /img/banners/home-banner-l-min.png 2x, 
                /img/banners/home-banner-xl-min.png 3x" type="image/png">
        <source media="(min-width: 1001px) and (max-width: 1500px)" srcset="/img/banners/home-banner-m-min.png, 
                /img/banners/home-banner-xl-min.png 2x" type="image/png">
        <source media="(min-width: 1501px) and (max-width: 2000px)" srcset="/img/banners/home-banner-l-min.png, 
                /img/banners/home-banner-xl-min.png 2x" type="image/png">
        <source media="(min-width: 2001px)  " srcset="/img/banners/home-banner-xl-min.png" type="image/png">

        <!-- Fallback -->
        <img src="/img/banners/home-banner-sm-min.png" alt="An Experienced Chef Bakes Pizza with a Special Giant Spatula." decoding="async" loading="lazy">
    </picture>
    <h1>Pizza Home</h1>
    <p>"Where Every Bite Takes You to Italy."</p>
    <a href="/menu" class="btn-primary">Order Now!</a>
    <a href="#best-sellers" class="arrow-link" style="position:absolute;">
        <img src="/img/icons/arrow-down.svg" class="arrow-down" alt="Arrow Down">
    </a>
</section>

<section id="best-sellers" class="best-sellers">
    <div class="container">
        <h2>Best Sellers</h2>
        <div class="article-container">

            <?php
            if (isset($this->viewData['bestSellers'])) {
                echo $this->viewData['bestSellers'];
            }
            ?>

        </div>
    </div>
</section>

<section class="team">
    <div class="container">
        <h2>Our Team</h2>
    </div>
</section>

<section class="location">
    <div class="container">
        <h2>Our Location</h2>
    </div>
</section>