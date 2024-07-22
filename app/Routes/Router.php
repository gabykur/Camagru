<?php

namespace App\Routes;

class Router {
    private $routes = [];
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function get($uri, $action) {
        $this->routes['GET'][$uri] = $action;
    }

    public function post($uri, $action) {
        $this->routes['POST'][$uri] = $action;
    }

    public function dispatch($uri, $method) {
        if (isset($this->routes[$method][$uri])) {
            $this->callAction(
                ...explode('@', $this->routes[$method][$uri])
            );
        } else {
            $this->handleNotFound();
        }
    }

    private function callAction($controller, $action) {
        $controller = "App\\Controllers\\{$controller}";
        $controller = new $controller($this->pdo);
        $controller->$action();
    }

    private function handleNotFound() {
        http_response_code(404);
        echo "404 Not Found";
        require '../Views/errors/404.php';
    }
}
