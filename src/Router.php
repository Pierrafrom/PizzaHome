<?php

namespace App;

use Exception;

/**
 * Class Router
 *
 * Handles the routing of requests to the appropriate controller and method.
 *
 * @package App
 */
class Router
{
    /**
     * @var array An associative array of routes where the key is the HTTP method
     *            and the value is an array of URL patterns and their associated
     *            controllers and methods.
     */
    private array $routes = [];

    /**
     * @var string The path to the view files.
     */
    private string $viewPath;

    /**
     * @var string Namespace prefix for controller classes.
     */
    private string $controllerNamespace = '\\App\\controllers\\';

    /**
     * Router constructor.
     *
     * @param string $viewPath The path to the view files.
     */
    public function __construct(string $viewPath)
    {
        $this->viewPath = $viewPath;
    }

    /**
     * Add a GET route.
     *
     * @param string $url The URL pattern to match.
     * @param string $controller The controller to use.
     * @param string $method The method of the controller to call (default is 'index').
     *
     * @return $this
     */
    public function get(string $url, string $controller, string $method = 'loadPage'): self
    {
        return $this->addRoute('GET', $url, $controller, $method);
    }

    /**
     * Add a POST route.
     *
     * @param string $url The URL pattern to match.
     * @param string $controller The controller to use.
     * @param string $method The method of the controller to call (default is 'index').
     *
     * @return $this
     */
    public function post(string $url, string $controller, string $method): self
    {
        return $this->addRoute('POST', $url, $controller, $method);
    }

    /**
     * Add a route.
     *
     * @param string $httpMethod The HTTP method (GET, POST, etc.)
     * @param string $url The URL pattern to match.
     * @param string $controller The controller to use.
     * @param string $method The method of the controller to call.
     *
     * @return $this
     */
    private function addRoute(string $httpMethod, string $url, string $controller, string $method): self
    {
        $this->routes[$httpMethod][$url] = ['controller' => $controller, 'method' => $method];
        return $this;
    }

    /**
     * Run the router: match the requested URL against the routes and call the
     * associated controller method, or throw an Exception if no match is found.
     *
     * @return $this
     *
     * @throws Exception When a route or method is not found.
     */
    public function run(): self
    {
        $requestedMethod = $_SERVER['REQUEST_METHOD'];
        $requestedPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        try {
            if (!isset($this->routes[$requestedMethod][$requestedPath])) {
                throw new Exception("404 Not Found", 404);
            }

            $route = $this->routes[$requestedMethod][$requestedPath];
            $fullControllerName = $this->controllerNamespace . $route['controller'];

            if (!class_exists($fullControllerName)) {
                throw new Exception("500 Internal Server Error - Controller Not Found", 500);
            }

            $controller = new $fullControllerName($this->viewPath);
            $method = $route['method'];

            if (!method_exists($controller, $method)) {
                throw new Exception("500 Internal Server Error - Method Not Found", 500);
            }

            $controller->$method();
            return $this;

        } catch (Exception $e) {
            http_response_code($e->getCode());
            echo $e->getMessage();
            return $this;
        }
    }
}
