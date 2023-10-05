<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php $title ?? 'Pizza Home' ?></title>
    <link rel="icon" href="/img/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="/css/style.css">
    <?php if (isset($cssFile)): ?>
        <link rel="stylesheet" href="/css/<?php echo $cssFile; ?>">
    <?php endif; ?>
    <script src="/js/navbar.js" defer></script>
</head>
<body>
<header>
    <nav>
        <ul>
            <li class="logo"><a href="/"><img src="/img/logo.svg" alt="Pizza home logo"></a></li>
            <li><a href="/"><span>Home</span></a></li>
            <li><a href="/menu">Menu</a></li>
            <li class="connexion-container">
                <div>
                    <button class="btn-secondary" id="login-button">Log In</button>
                    <button class="btn-primary" id="signup-button">Sign Up</button>
                </div>
            </li>
            <li class="avatar-container">
                <div class="dropdown">
                    <label for="avatar-menu" class="dropdown-label">
                        <img src="/img/avatar.png" alt="Avatar">
                    </label>
                    <input type="checkbox" id="avatar-menu">
                    <div class="dropdown-content">
                        <a href="#">Cart</a>
                        <a href="#">Order History</a>
                        <hr>
                        <button class="btn-error" id="logout-button">Log Out</button>
                    </div>
                </div>

            </li>
            <li class="menu-toggle">
                <!-- Thanks to JulanDeAlb for the hamburger button code
                    You can find it here:
                    https://uiverse.io/JulanDeAlb/tall-swan-6 -->
                <label class="hamburger" for="hamburger-menu">
                    <input type="checkbox" id="hamburger-menu">
                    <svg viewBox="0 0 32 32">
                        <path class="line line-top-bottom"
                              d="M27 10 13 10C10.8 10 9 8.2 9 6 9 3.5 10.8 2 13 2 15.2 2 17 3.8 17 6L17 26C17 28.2 18.8
                               30 21 30 23.2 30 25 28.2 25 26 25 23.8 23.2 22 21 22L7 22"></path>
                        <path class="line" d="M7 16 27 16"></path>
                    </svg>
                </label>
            </li>
        </ul>
    </nav>
</header>

<main>
    <?php echo $content; ?>
</main>

<footer>
    <p>&copy; <?php echo date('Y'); ?> - Pizza Home</p>
    <?php if ($_ENV['ENVIRONMENT'] == 'development') {
        echo 'Page rendered in ' . round(1000 * (microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'])) . ' ms';
    } ?>
</footer>
</body>
</html>