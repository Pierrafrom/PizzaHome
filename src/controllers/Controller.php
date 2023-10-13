<?php

namespace App\controllers;

use Exception;
use ReflectionClass;

/**
 * Class Controller
 *
 * An abstract base controller to manage view rendering.
 */
abstract class Controller
{
    /**
     * @var string The base path for the view files.
     */
    protected string $viewPath;

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
     * Render a view based on the implementing controller class.
     *
     * The method identifies the corresponding view file based on the
     * name of the derived controller class, renders it, and includes
     * it within a layout.
     *
     * @return void
     * @throws Exception If the view or layout files are not found.
     */
    public function index(): void
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

            $layoutFilePath = $this->getFilePath('layouts', 'default');

            if (!file_exists($layoutFilePath)) {
                throw new Exception("Layout file [$layoutFilePath] not found.");
            }

            require $layoutFilePath;
        } catch (Exception $e) {
            // Handle the exception, e.g., by logging the error and displaying an error message to the user.
            error_log($e->getMessage());
            echo "An error occurred while loading the page.";
        }
    }

    /**
     * Determine the view name based on the implementing controller class name.
     *
     * @return string The name of the view associated with the controller.
     */
    protected function getViewName(): string
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
    protected function getFilePath(string $type, string $name): string
    {
        return $this->viewPath . DIRECTORY_SEPARATOR . $type . DIRECTORY_SEPARATOR . $name . '.php';
    }
}
