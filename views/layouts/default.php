<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php $title ?? 'Pizza Home'?></title>
</head>
<body>
<header>
    <nav>
        <ul>
            <li><a href="/">Home</a></li>
            <li><a href="#">About us</a></li>
            <li><a href="#">Contact</a></li>
        </ul>
    </nav>
</header>

<main>
    <?php echo $content; ?>
</main>

<footer>
    <p>&copy; <?php echo date('Y'); ?> - Pizza Home</p>
    <?php if ($_ENV['ENVIRONMENT'] == 'development'){
        echo 'Page rendered in ' . round(1000 * (microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'])) . ' ms';
    } ?>
</footer>
</body>
</html>