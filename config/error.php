<?php

// Retrieve the value of the 'ENVIRONMENT' environment variable and store it in the $environment variable.
$environment = $_ENV['ENVIRONMENT'];

// Check if the application environment is set to 'development'.
if ($environment === 'development') {
    // Create a new instance of the Whoops error handler.
    $whoops = new Whoops\Run;

    // Push a PrettyPageHandler to Whoops for displaying detailed error information.
    $whoops->pushHandler(new Whoops\Handler\PrettyPageHandler);

    // Register the Whoops error handler, which will display detailed error pages in development.
    $whoops->register();

    // Set PHP configuration settings to display errors and startup errors for debugging.
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
} else {
    // In a non-development environment, turn off error display for security and production reasons.
    ini_set('display_errors', '0');
    ini_set('display_startup_errors', '0');
}

// Set the error reporting level to include all errors (E_ALL) regardless of the environment.
error_reporting(E_ALL);
