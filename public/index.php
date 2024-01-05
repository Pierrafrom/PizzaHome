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
        ->post('/checkoutSubmit', 'CheckoutController', 'checkout') // Checkout post route.
        ->get('/details', 'ProductController') // Product details page route.
        ->get('/creation', 'CreationController') // Object creation page route.
        ->post('/createObject', 'CreationController', 'createObject') // Object creation post route.

        // API routes.
        ->post('/api/verifyPassword', 'ApiController', 'verifyPassword') // Verify password route.
        ->post('/api/checkEmailExists', 'ApiController', 'checkEmailExists') // Check email exists route.
        ->post('/api/addProductToCart', 'ApiController', 'addProductToCart') // Add product to cart route.
        ->post('/api/removeProductFromCart', 'ApiController', 'removeProductFromCart') // Remove product from cart route.
        ->post('/api/updateProductQuantity', 'ApiController', 'updateProductQuantity') // Update product quantity route.
        ->post('/api/updatestockQuantity', 'ApiController', 'updatestockQuantity') // Update stock quantity route.
        ->post('/api/addCustomPizzaToCart', 'ApiController', 'addCustomPizzaToCart') // Add custom pizza to cart route.
        ->post('/api/deleteItem', 'ApiController', 'deleteItem') // Delete item route.
        ->post('/api/getTopProducts', 'ApiController', 'getTopProducts') // Get top products route.
        ->post('/api/getSalesByMonth', 'ApiController', 'getSalesByMonth') // Get sales by month route.
        ->post('/api/getPizzaStats', 'ApiController', 'getPizzaStats') // Get pizza stats route.
        ->post('/api/updateSpotlight', 'ApiController', 'updateSpotlight') // Update spotlight route.

        // Handle the request.
        ->run();
}
