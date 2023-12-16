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

    $router->get('/', 'HomeController') // Home page route.
    ->get('/menu', 'MenuController') // Menu page route.
    ->get('/registration', 'RegistrationController') // Registration page route.
    ->get('/cart', 'CartController') // Cart page route.
    ->post('/login', 'RegistrationController', 'login') // Login post route.
    ->post('/signin', 'RegistrationController', 'signin') // signup post route.
    ->get('/logout', 'RegistrationController', 'logout') // Logout route.
    ->get('/admin', 'AdminController') // Admin page route.
    ->get('/checkout', 'CheckoutController') // Checkout page route.

    // API routes.
    ->post('/api/verifyPassword', 'ApiController', 'verifyPassword')
    ->post('/api/checkEmailExists', 'ApiController', 'checkEmailExists')
    ->post('/api/addProductToCart', 'ApiController', 'addProductToCart')
    ->post('/api/removeProductFromCart', 'ApiController', 'removeProductFromCart')
    ->post('/api/updateProductQuantity', 'ApiController', 'updateProductQuantity')

    // Handle the request.
    ->run();
}
