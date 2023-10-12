<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo $title ?? 'Pizza Home'; ?></title>
    <link rel="icon" href="/img/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="/css/style.css" type="text/css">
    <link rel="preload" href="/css/style.css" as="style">
    <link rel="stylesheet" href="/css/hamburger.css" type="text/css">
    <link rel="preload" href="/css/hamburger.css" as="style">
    <link rel="stylesheet" href="/css/font.css" type="text/css">
    <link rel="preload" href="/css/font.css" as="style">
    <?php if (isset($cssFiles)): ?>
        <?php foreach ($cssFiles as $cssFile): ?>
            <link rel="stylesheet" href="/css/<?php echo $cssFile; ?>" type="text/css">
            <link rel="preload" href="/css/<?php echo $cssFile; ?>" as="style">
        <?php endforeach; ?>
    <?php endif; ?>
    <script src="/js/navbar.js" defer></script>
    <?php if (isset($scriptFile)): ?>
        <script src="/js/<?php echo $scriptFile; ?>" defer></script>
    <?php endif; ?>
</head>
<body>

<?php
$isConnected = false;
?>

<header>
    <nav>
        <div><a href="/"><img src="/img/logo.svg" alt="Logo"></a></div>
        <div id="nav-content" class="nav-content collapse hide">
            <?php if ($isConnected): ?>
                <ul class="nav-links" style="width: calc(100% - 48px);">
                    <li><a href="/"><span>Home</span></a></li>
                    <li><a href="/menu">Menu</a></li>
                    <li><a href="#">Cart</a></li>
                </ul>
                <div class="user-info">
                    <div><img id="avatar-img" src="/img/avatar.png" alt="User-avatar"></div>
                    <ul id="user-dropdown" class="hide">
                        <li><a href="#">Order History</a></li>
                        <li><a href="#">Profile</a></li>
                        <li>
                            <hr>
                        </li>
                        <li><a href="#" class="btn-error">Log Out</a></li>
                    </ul>
                </div>
            <?php else: ?>
                <ul class="nav-links">
                    <li><a href="/"><span>Home</span></a></li>
                    <li><a href="/menu">Menu</a></li>
                    <li><a href="#">Cart</a></li>
                </ul>
                <div class="auth-buttons">
                    <a href="#" class="btn-secondary">Log In</a>
                    <a href="#" class="btn-primary">Sign Up</a>
                </div>
            <?php endif; ?>
        </div>
        <div id="hamburger-icon" class="menu-toggle hide">
            <!-- Thanks to JulanDeAlb for the hamburger button code
                    You can find it here:
                    https://uiverse.io/JulanDeAlb/tall-swan-6 -->
            <label class="hamburger" for="hamburger-checkbox">
                <input type="checkbox" id="hamburger-checkbox">
                <svg viewBox="0 0 32 32">
                    <path class="line line-top-bottom" d="M27 10 13 10C10.8 10 9 8.2 9 6 9 3.5 10.8 2 13 2 15.2 2 17 3.8 17 6L17 26C17 28.2 18.8
                               30 21 30 23.2 30 25 28.2 25 26 25 23.8 23.2 22 21 22L7 22"></path>
                    <path class="line" d="M7 16 27 16"></path>
                </svg>
            </label>
        </div>
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