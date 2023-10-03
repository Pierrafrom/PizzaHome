<?php

namespace App;

use AltoRouter;
use Exception;

class Router
{
    private string $viewPath;
    private AltoRouter $router;

    public function __construct(string $viewPath)
    {
        $this->viewPath = $viewPath;
        $this->router = new AltoRouter();
    }

    public function get(string $url, string $view, ?string $name = null): self
    {
        try {
            $this->router->map('GET', $url, $view, $name);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        return $this;
    }

    public function run(): self
    {
        $match = $this->router->match();
        $view = $match['target'];
        ob_start();
        require $this->viewPath . DIRECTORY_SEPARATOR . $view . '.php';
        $content = ob_get_clean();
        require $this->viewPath . DIRECTORY_SEPARATOR . 'layouts/default.php';

        return $this;
    }

}