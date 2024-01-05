<?php

namespace App\controllers;

use App\helpers\SessionHelper;
use Exception;
use InvalidArgumentException;
use ReflectionClass;

/**
 * Abstract base Controller class for managing view rendering.
 * 
 * This class provides common functionalities to be used by derived controller classes.
 * It includes handling of view rendering, loading necessary scripts and styles, and managing view data.
 * 
 * Methods included in this class:
 * 
 * - __construct(string $viewPath): Initializes the controller with the base path for view files.
 * - __get(string $property): Magic getter to access protected properties.
 * - loadPage(): Renders a view based on the implementing controller class.
 * - getViewName(): Determines the view name based on the implementing controller class name.
 * - getFilePath(string $type, string $name): Builds the file path for a specific view or layout.
 * - loadMenu(): Loads and prepares the menu HTML based on the user's session status.
 * - loadHeadSection(): Loads and prepares the head section of the HTML page.
 *
 * @package App\controllers
 */
abstract class Controller
{
    /**
     * The default title of the web page.
     *
     * This static property holds the default title that can be used across different views.
     * Being static, it allows for a single point of update if the default title needs to be changed,
     * without needing to instantiate the Controller class.
     *
     * @var string
     */
    protected static string $title = "Pizza Home";

    /**
     * A list of CSS files to be included in the web page.
     *
     * This static property can hold an array of CSS filenames that are relevant to the entire application.
     * As a static property, it provides a centralized place to manage CSS file references
     * that can be used or modified by different instances or subclasses of the Controller.
     *
     * @var ?array
     */
    protected static ?array $cssFiles = null;

    /**
     * A list of JavaScript files to be included in the web page.
     *
     * Similar to $cssFiles, this static property holds an array of JavaScript filenames.
     * These scripts are intended to be common across various parts of the application.
     * Utilizing a static property allows for easy maintenance and updates application-wide.
     *
     * @var ?array
     */
    protected static ?array $scriptFiles = null;

    /**
     * A list of JavaScript module files to be included in the web page.
     *
     * This static property is used to manage module script files that are used across the application.
     * The use of static here ensures that any changes to the modules need to be updated only in one place,
     * reflecting across all instances where they are used.
     *
     * @var ?array
     */
    protected static ?array $moduleFiles = null;

    /**
     * A list of JavaScript files to be included in the web page.
     *
     * This static property holds an array of JavaScript filenames.
     * These scripts are intended to be common across various parts of the application.
     * Utilizing a static property allows for easy maintenance and updates application-wide.
     *
     * @var array|null
     */
    protected static ?array $scriptLinkFiles = null;

    /**
     * @var string The base path for the view files.
     */
    protected string $viewPath;

    /**
     * @var array An associative array of data to be passed to the view.
     */
    protected array $viewData = [];

    /**
     * Controller constructor.
     *
     * @param string $viewPath The base path for the view files.
     */
    public function __construct(string $viewPath)
    {
        $this->viewPath = $viewPath;
    }

    /**
     * Magic getter to access protected properties of the class.
     *
     * @param string $property The property name to access.
     *
     * @return mixed The value of the property.
     * @throws InvalidArgumentException If the property does not exist.
     */
    public function __get(string $property)
    {
        if (property_exists($this, $property)) {
            return $this->$property;
        }
        throw new InvalidArgumentException("Invalid property: $property");
    }

    /**
     * Render a view based on the implementing controller class.
     *
     * The method identifies the corresponding view file based on the
     * name of the derived controller class, renders it, and includes
     * it within a layout.
     *
     * @return void
     * @throws Exception If the view or layout files are not found.
     */
    public function loadPage(): void
    {
        try {
            $view = $this->getViewName();

            ob_start();
            $viewFilePath = $this->getFilePath('pages', $view);

            if (!file_exists($viewFilePath)) {
                throw new Exception("View file [$viewFilePath] not found.");
            }

            require $viewFilePath;
            $content = ob_get_clean();

            $this->loadMenu();
            $this->loadHeadSection();

            $layoutFilePath = $this->getFilePath('layouts', 'default');

            if (!file_exists($layoutFilePath)) {
                throw new Exception("Layout file [$layoutFilePath] not found.");
            }

            require $layoutFilePath;
        } catch (Exception $e) {
            // Handle the exception, e.g., by logging the error and displaying an error message to the user.
            echo ($e->getMessage());
            echo "An error occurred while loading the page.";
        }
    }

    /**
     * Determine the view name based on the implementing controller class name.
     *
     * @return string The name of the view associated with the controller.
     */
    private function getViewName(): string
    {
        return strtolower(str_replace('Controller', '', (new ReflectionClass($this))->getShortName()));
    }

    /**
     * Build the file path for a specific view or layout.
     *
     * @param string $type The type of file, e.g., 'pages' or 'layouts'.
     * @param string $name The name of the file without extension.
     * @return string The full file path.
     */
    private function getFilePath(string $type, string $name): string
    {
        return $this->viewPath . DIRECTORY_SEPARATOR . $type . DIRECTORY_SEPARATOR . $name . '.php';
    }

    /**
     * Loads and prepares the menu HTML based on the user's session status.
     *
     * This function checks if a user is logged in (via session data) and prepares
     * an appropriate HTML menu string accordingly. If the user is logged in, the menu includes
     * user-specific options like 'Order History' and 'Log Out'. Otherwise, it shows general options
     * with login and sign-in links.
     */
    private function loadMenu(): void
    {
        // Initialize the session to manage user login state.
        SessionHelper::initSession();
        $isConnected = false;

        // Check if the user is logged in by verifying session data.
        if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
            $isConnected = true;
        }

        $totalItems = CartController::getTotalItemsInCart();

        // Prepare the HTML menu string based on the user's login status.
        $menu = "";
        if ($isConnected) {
            // Menu HTML for logged-in users.
            $menu = "<ul class='nav-links' style='width: calc(100% - 48px);'>
                    <li><a href='/'><span>Home</span></a></li>
                    <li><a href='/menu'>Menu</a></li>
                    <li class=\"cart-icon\">
                        <a href=\"/cart\">
                            <img src=\"/img/icons/cart.svg\" alt=\"Cart\">
                            <span id=\"cart-count\" class=\"cart-count\">$totalItems</span> 
                        </a>
                    </li>
                </ul>
                <div class='user-info'>
                    <div><img id='avatar-img' src='/img/avatar.png' alt='User-avatar'></div>
                    <ul id='user-dropdown' class='hide'>
                        <li><a href='#'>Order History</a></li>
                        <li><a href='#'>Profile</a></li>";
            if (SessionHelper::is_admin()) {
                $menu .= "<li><a href='/admin'>Admin</a></li>";
            }
            $menu .= "<li>
                            <hr>
                        </li>
                        <li><a href='/logout?redirect=" . urlencode($_SERVER['REQUEST_URI']) . "' 
                               class='btn-error'>Log Out</a></li>
                    </ul>
                </div>";
        } else {
            // Menu HTML for guests (not logged in).
            $menu = "<ul class='nav-links'>
                    <li><a href='/'><span>Home</span></a></li>
                    <li><a href='/menu'>Menu</a></li>
                    <li class=\"cart-icon\">
                        <a href=\"/cart\">
                            <img src=\"/img/icons/cart.svg\" alt=\"Cart\">
                            <span id=\"cart-count\" class=\"cart-count\">$totalItems</span> 
                        </a>
                    </li>
                </ul>
                <div class='auth-buttons'>
                    <a href='/registration?redirect=" . urlencode($_SERVER['REQUEST_URI']) . "#login'
                       class='btn-secondary'>Log In</a>
                    <a href='/registration?redirect=" . urlencode($_SERVER['REQUEST_URI']) . "#signin'
                       class='btn-primary'>Sign In</a>
                </div>";
        }

        // Store the menu HTML in the view data for use in the view templates.
        $this->viewData['menu'] = $menu;
    }

    /**
     * Loads and prepares the head section of the HTML page.
     *
     * This function constructs the HTML <head> section, including meta tags, title, and links to stylesheets and scripts.
     * It dynamically adds CSS and JavaScript files based on the static properties of the class. The title is also set dynamically.
     */
    private function loadHeadSection(): void
    {
        // Start constructing the head section HTML string.
        $head = '<head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <meta http-equiv="X-UA-Compatible" content="ie=edge">
                <title>' . (self::$title) . '</title>
                <link rel="icon" href="/img/favicon.ico" type="image/x-icon">
                <link rel="stylesheet" href="/css/style.css" type="text/css">
                <link rel="preload" href="/css/style.css" as="style">
                <link rel="stylesheet" href="/css/hamburger.css" type="text/css">
                <link rel="preload" href="/css/hamburger.css" as="style">
                <link rel="stylesheet" href="/css/font.css" type="text/css">
                <link rel="preload" href="/css/font.css" as="style">';

        // Dynamically add CSS files if they are set.
        if (isset(self::$cssFiles)) {
            foreach (self::$cssFiles as $cssFile) {
                $head .= '<link rel="stylesheet" href="/css/' . $cssFile . '" type="text/css">
                      <link rel="preload" href="/css/' . $cssFile . '" as="style">';
            }
        }

        // Add default scripts.
        $head .= '<script src="/js/navbar.js" defer></script>
              <script src="/js/loader.js" defer></script>';

        // Dynamically add additional JavaScript files if they are set.
        if (isset(self::$scriptFiles)) {
            foreach (self::$scriptFiles as $scriptFile) {
                $head .= '<script src="/js/' . $scriptFile . '" defer></script>';
            }
        }

        // Dynamically add module script files if they are set.
        if (isset(self::$moduleFiles)) {
            foreach (self::$moduleFiles as $moduleFile) {
                $head .= '<script type="module" src="/js/' . $moduleFile . '" defer></script>';
            }
        }

        // Dynamically add additional JavaScript link files if they are set.
        if (isset(self::$scriptLinkFiles)) {
            foreach (self::$scriptLinkFiles as $scriptLinkFile) {
                $head .= '<script src="' . $scriptLinkFile . '" defer></script>';
            }
        }

        // Store the head section HTML in the view data for use in the view templates.
        $this->viewData['head'] = $head;
    }
}
