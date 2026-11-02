<?php

declare(strict_types=1);

namespace App\Helpers;

class Router
{
    private static array $routes = [];

    public static function get(string $path, array $handler, array $middlewares = []): void
    {
        self::addRoute('GET', $path, $handler, $middlewares);
    }

    public static function post(string $path, array $handler, array $middlewares = []): void
    {
        self::addRoute('POST', $path, $handler, $middlewares);
    }

    private static function addRoute(string $method, string $path, array $handler, array $middlewares): void
    {
        self::$routes[] = [
            'method' => $method,
            'path' => $path,
            'controller' => $handler[0],
            'action' => $handler[1],
            'middlewares' => $middlewares,
        ];
    }

    public static function dispatch(string $method, string $uri): void
    {
        // Loại bỏ query string
        $uri = parse_url($uri, PHP_URL_PATH);
        $uri = rtrim($uri, '/');
        if ($uri === '') {
            $uri = '/';
        }

        foreach (self::$routes as $route) {
            if ($route['method'] === $method && $route['path'] === $uri) {
                // Chạy middlewares
                foreach ($route['middlewares'] as $middlewareClass) {
                    $middleware = new $middlewareClass();
                    $middleware->handle();
                }

                $controllerClass = $route['controller'];
                $action = $route['action'];

                $controller = new $controllerClass();
                $controller->$action();
                return;
            }
        }

        // Không tìm thấy route -> 404
        http_response_code(404);
        require ROOT_PATH . '/resources/views/errors/404.php';
        exit;
    }
}
