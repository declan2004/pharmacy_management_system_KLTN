<?php
class Router {
    private $routes = [];

    // Khai báo đường dẫn mới vào hệ thống
    public function add($method, $route, $controller, $action) {
        $this->routes[] = [
            'method' => $method,
            'route' => $route,
            'controller' => $controller,
            'action' => $action
        ];
    }

    // Điều hướng URL người dùng gõ tới đúng Controller và Action
    public function dispatch($url) {
        $url = '/' . trim($url, '/'); // Chuẩn hóa URL
        $method = $_SERVER['REQUEST_METHOD'];

        foreach ($this->routes as $route) {
            if ($route['method'] == $method && $route['route'] == $url) {
                $controllerName = $route['controller'];
                require_once '../app/controllers/' . $controllerName . '.php';
                
                $controller = new $controllerName();
                $action = $route['action'];
                $controller->$action();
                return;
            }
        }
        
        header("HTTP/1.0 404 Not Found");
        die("404 Not Found - The requested page does not exist.");
    }
}