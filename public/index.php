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
try {
    initializeRouter(__DIR__ . '/../views');
} catch (Exception $e) {
    // Log the exception.
    error_log($e->getMessage());
}

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
 * @throws Exception
 */
function initializeRouter(string $viewsPath): void
{
    $router = new App\Router($viewsPath);

    // Home page route.
    $router->get('/', 'HomeController');

    // Menu page route.
    $router->get('/menu', 'MenuController');

    // Registration page route.
    $router->get('/registration', 'RegistrationController');

    // Cart page route.
    $router->get('/cart', 'CartController');

    // Login post route.
    $router->post('/login', 'RegistrationController', 'login');

    // signup post route.
    $router->post('/signin', 'RegistrationController', 'signin');

    // Logout route.
    $router->get('/logout', 'RegistrationController', 'logout');

    // Admin page route.
    $router->get('/admin', 'AdminController');

    // API route.
    $router->post('/api/verifyPassword', 'ApiController', 'verifyPassword');

    $router->post('/api/checkEmailExists', 'ApiController', 'checkEmailExists');

    $router->post('/api/addProductToCart', 'ApiController', 'addProductToCart');

    // Handle the request.
    $router->run();
}
