<?php
declare(strict_types=1);

namespace App\Core;

class Router
{
    private array $routes = [
        'GET' => [],
        'POST' => [],
    ];

    public function get(string $path, callable|array $handler): void
    {
        $this->routes['GET'][$this->normalize($path)] = $handler;
    }

    public function post(string $path, callable|array $handler): void
    {
        $this->routes['POST'][$this->normalize($path)] = $handler;
    }

    public function dispatch(string $method, string $uri): void
    {
        $path = $this->normalize($uri);
        $handler = $this->routes[$method][$path] ?? null;

        if ($handler === null) {
            http_response_code(404);
            echo 'Ruta no encontrada';
            return;
        }

        if (is_array($handler)) {
            [$controllerClass, $methodName] = $handler;
            $controller = new $controllerClass();
            $controller->{$methodName}();
            return;
        }

        $handler();
    }

    private function normalize(string $path): string
    {
        $cleanPath = '/' . ltrim($path, '/');
        $base = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');

        if ($base && strpos($cleanPath, $base) === 0) {
            $cleanPath = substr($cleanPath, strlen($base));
        }

        $cleanPath = rtrim($cleanPath, '/') ?: '/';
        return $cleanPath;
    }
}
