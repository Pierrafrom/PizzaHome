<?php

require_once dirname(__DIR__) . '/vendor/autoload.php';
require_once dirname(__DIR__) . '/config/error.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(dirname(__DIR__) . '/config');
$dotenv->load();

$environment = getenv('ENVIRONMENT');

$router = new AltoRouter();

try {
    $router->map('GET', '/', function () {
        require dirname(__DIR__) . '/views/pages/home.php';
    });
} catch (Exception $e) {
    echo $e->getMessage();
}

$match = $router->match();
$match['target']();