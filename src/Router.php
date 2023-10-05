<?php

namespace App;

use AltoRouter;
use Exception;

class Router
{
    // Private attribute to store the path to views directory.
    private string $viewPath;

    // Private attribute to store an instance of AltoRouter.
    private AltoRouter $router;

    /**
     * Constructor for the Router class.
     *
     * @param string $viewPath The path to the view's directory.
     */
    public function __construct(string $viewPath)
    {
        // Initialize the viewPath attribute with the provided views directory path.
        $this->viewPath = $viewPath;

        // Create a new instance of AltoRouter for routing.
        $this->router = new AltoRouter();
    }

    /**
     * Define a GET route.
     *
     * @param string      $url   The URL path for the route.
     * @param string      $view  The name of the associated view.
     * @param string|null $name  (Optional) The name of the route.
     *
     * @return self Returns the Router instance for method chaining.
     */
    public function get(string $url, string $view, ?string $name = null): self
    {
        try {
            // Map a GET route with the provided URL, associated view, and optional name.
            $this->router->map('GET', $url, $view, $name);
        } catch (Exception $e) {
            // Handle exceptions and display error messages.
            echo $e->getMessage();
        }

        // Return the Router instance to allow method chaining.
        return $this;
    }

    /**
     * Run the router and render the associated view.
     *
     * @return self Returns the Router instance for method chaining.
     */
    public function run(): self
    {
        // Attempt to match the current request against defined routes.
        $match = $this->router->match();

        // Get the target view associated with the matched route.
        $view = $match['target'];

        // Start output buffering to capture the view content.
        ob_start();

        // Include the associated view file.
        require $this->viewPath . DIRECTORY_SEPARATOR . $view . '.php';

        // Get and clean the buffered view content.
        $content = ob_get_clean();

        // Include the default layout file and display the view content.
        require $this->viewPath . DIRECTORY_SEPARATOR . 'layouts/default.php';

        // Return the Router instance to allow method chaining.
        return $this;
    }
}
