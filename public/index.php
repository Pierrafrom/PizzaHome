<?php

/**
 * Index file to bootstrap the application.
 *
 * This file sets up and configures the application environment, including
 * autoloading dependencies, initializing configurations, and setting up routing.
 * It acts as the front controller, handling all requests and directing them
 * through the appropriate routes.
 *
 * PHP version 8.2.11
 *
 * @category Index
 * @package  App
 */


// Autoload dependencies.
require_once __DIR__ . '/../vendor/autoload.php';

// Load configurations.
initializeConfigurations(__DIR__ . '/../config');

// Initialize routing and handle the request.
initializeRouter(__DIR__ . '/../views');

/**
 * Initialize application configurations.
 *
 * @param string $configPath The path to the configuration directory.
 *
 * @return void
 */
function initializeConfigurations(string $configPath): void
{
    // Load environment variables.
    $dotenv = Dotenv\Dotenv::createImmutable($configPath);
    $dotenv->load();

    // Include error handling configuration.
    require_once $configPath . '/error.php';
}

/**
 * Initialize the router, define routes, and handle the incoming request.
 *
 * @param string $viewsPath The path to the views directory.
 *
 * @return void
 */
function initializeRouter(string $viewsPath): void
{
    $router = new App\Router($viewsPath);

    // Home page route.
    $router->get('/', 'HomeController');

    // Menu page route.
    $router->get('/menu', 'MenuController');

    // Handle the request.
    $router->run();
}
