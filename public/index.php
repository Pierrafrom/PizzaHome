<?php
require __DIR__ . '/../vendor/autoload.php';

$router = new AltoRouter();

try {
    $router->map('GET', '/', function () {
        echo 'Hello World';
    });
} catch (Exception $e) {
    echo $e->getMessage();
}
