<?php

$title = "Registration";
$cssFiles = ["banner.css", "registration.css"];
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
    <h1>Registration</h1>
</section>

<section>
    <div class="registration-container">
        <ul class="tabs" role="tablist">
            <li><a href="#signin" class="tab active-tab" role="tab" aria-controls="pizza-tab" aria-selected="true"
                   id="tab1">Sign In</a>
            </li>
            <li><a href="#login" class="tab" role="tab" aria-controls="drink-tab" aria-selected="false"
                   id="tab2">Log In</a>
            </li>
        </ul>
        <div id="login" class="tab-content hide">
            <form action="/login" method="post">
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <input type="hidden" name="redirect" value="<?= htmlspecialchars($_GET['redirect'] ?? '') ?>">
                <input type="submit" value="Login">
            </form>
        </div>

        <div id="signin" class="tab-content show-flex">
            <form action="/signin" method="post">
                <input type="text" name="lastname" placeholder="Last Name" required>
                <input type="text" name="firstname" placeholder="First Name" required>

                <!-- Sélection du code pays -->
                <select name="country_code" required>
                    <option value="+33">France (+33)</option>
                    <option value="+1">USA (+1)</option>
                </select>
                <input type="tel" name="phone" placeholder="Numéro de téléphone" pattern="\d{9,10}"
                       title="Numéro de téléphone sans espace et sans le code pays." required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <input type="password" name="confirm_password" placeholder="Confirm Password" required>
                <input type="hidden" name="redirect" value="<?= htmlspecialchars($_GET['redirect'] ?? '') ?>">
                <input type="submit" value="Create Account">
            </form>
        </div>
    </div>
</section>