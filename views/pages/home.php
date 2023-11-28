<section class="banner home-banner">
    <picture>
        <!-- WebP Format -->
        <source media="(max-width: 1000px)"
                srcset="/img/banners/home-banner-sm-min.webp, 
                /img/banners/home-banner-l-min.webp 2x, 
                /img/banners/home-banner-xl-min.webp 3x"
                type="image/webp">
        <source media="(min-width: 1001px) and (max-width: 1500px)"
                srcset="/img/banners/home-banner-m-min.webp, 
                /img/banners/home-banner-xl-min.webp 2x"
                type="image/webp">
        <source media="(min-width: 1501px) and (max-width: 2000px)"
                srcset="/img/banners/home-banner-l-min.webp, 
                /img/banners/home-banner-xl-min.webp 2x"
                type="image/webp">
        <source media="(min-width: 2001px)"
                srcset="/img/banners/home-banner-xl-min.webp"
                type="image/webp">

        <!-- PNG Format -->
        <source media="(max-width: 1000px)"
                srcset="/img/banners/home-banner-sm-min.png, 
                /img/banners/home-banner-l-min.png 2x, 
                /img/banners/home-banner-xl-min.png 3x"
                type="image/png">
        <source media="(min-width: 1001px) and (max-width: 1500px)"
                srcset="/img/banners/home-banner-m-min.png, 
                /img/banners/home-banner-xl-min.png 2x"
                type="image/png">
        <source media="(min-width: 1501px) and (max-width: 2000px)"
                srcset="/img/banners/home-banner-l-min.png, 
                /img/banners/home-banner-xl-min.png 2x"
                type="image/png">
        <source media="(min-width: 2001px)  "
                srcset="/img/banners/home-banner-xl-min.png"
                type="image/png">

        <!-- Fallback -->
        <img src="/img/banners/home-banner-sm-min.png"
             alt="An Experienced Chef Bakes Pizza with a Special Giant Spatula."
             decoding="async"
             loading="lazy"
        >
    </picture>
    <h1>Pizza Home</h1>
    <p>"Where Every Bite Takes You to Italy."</p>
    <a href="/menu" class="btn-primary">Order Now!</a>
    <a href="#best-sellers" class="arrow-link" style="position:absolute;">
        <svg width="40px" height="40px" viewBox="0 -4.5 20 20" xmlns="http://www.w3.org/2000/svg"
             xmlns:xlink="http://www.w3.org/1999/xlink" class = "arrow-down">

            <title>arrow_down [#338]</title>
            <desc>Created with Sketch.</desc>
            <defs>

            </defs>
            <g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                <g id="Dribbble-Light-Preview" transform="translate(-220.000000, -6684.000000)" fill="#ffffff">
                    <g id="icons" transform="translate(56.000000, 160.000000)">
                        <path d="M164.292308,6524.36583 L164.292308,6524.36583 C163.902564,6524.77071
                        163.902564,6525.42619 164.292308,6525.83004 L172.555873,6534.39267 C173.33636,6535.20244
                        174.602528,6535.20244 175.383014,6534.39267 L183.70754,6525.76791 C184.093286,6525.36716
                        184.098283,6524.71997 183.717533,6524.31405 C183.328789,6523.89985 182.68821,6523.89467
                        182.29347,6524.30266 L174.676479,6532.19636 C174.285736,6532.60124 173.653152,6532.60124
                        173.262409,6532.19636 L165.705379,6524.36583 C165.315635,6523.96094 164.683051,6523.96094
                        164.292308,6524.36583"
                              id="arrow_down-[#338]">

                        </path>
                    </g>
                </g>
            </g>
        </svg>
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
