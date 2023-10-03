<?php

$environment = $_ENV['ENVIRONMENT'];

if ($environment === 'development') {
    $whoops = new Whoops\Run;
    $whoops->pushHandler(new Whoops\Handler\PrettyPageHandler);
    $whoops->register();
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
} else {
    ini_set('display_errors', '0');
    ini_set('display_startup_errors', '0');
}
error_reporting(E_ALL);