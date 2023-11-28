<!DOCTYPE html>
<html lang="en">
<?php
echo $this->viewData['head'];
?>
<body>
<div class="page-container">
    <!-- loader -->
    <div id="loader">
        <svg class="loader-svg" viewBox="0 0 50 50">
            <circle cx="25" cy="10" r="4" fill="#4FA053"/>
            <circle cx="10" cy="40" r="4" fill="white"/>
            <circle cx="40" cy="40" r="4" fill="#AD2831"/>
        </svg>
    </div>

    <header>
        <nav>
            <div><a href="/"><img src="/img/logo.svg" decoding="async" loading="lazy" alt="Logo"></a></div>
            <div id="nav-content" class="nav-content hide">
                <?php
                echo $this->viewData['menu'];
                ?>
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
</div>
<footer>
    <p>&copy; <?php echo date('Y'); ?> - Pizza Home</p>
    <?php if ($_ENV['ENVIRONMENT'] == 'development') {
        echo 'Page rendered in ' . round(1000 * (microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'])) . ' ms';
    } ?>
</footer>

<script>
    document.body.classList.add('loader-active');
</script>

</body>
</html>