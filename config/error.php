<?php
$environment = getenv('ENVIRONMENT');

if ($environment === 'development') {
    $whoops = new Whoops\Run;
    $whoops->pushHandler(new Whoops\Handler\PrettyPageHandler);
    $whoops->register();
}