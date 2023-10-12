<?php

// Include the autoload.php file from the 'vendor' directory to load required classes automatically.
require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

// Load environment variables from a .env file located in the 'config' directory.
$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'config');
$dotenv->load();

// Include error handling configuration from the 'config' directory.
require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'error.php';

require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'DB_Connection.php';

// Create a new instance of the Router class, specifying the 'views' directory as its base path.
$router = new App\Router(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'views');

// Define routes for the application.
$router
    ->get('/', 'pages/home')
    ->get('/menu', 'pages/menu')

// Start the router to handle incoming requests.
    ->run();
