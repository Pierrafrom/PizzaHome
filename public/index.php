<?php

require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'config');
$dotenv->load();

require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'error.php';

$router = new App\Router(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'views');
$router
    ->get('/', 'pages/home')
    ->run();
