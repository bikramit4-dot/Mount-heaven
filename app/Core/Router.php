<?php

namespace App\Core;

class Router
{
    private array $routes = [];

    public function get(string $path, array|callable $handler): void
    {
        $this->add('GET', $path, $handler);
    }

    public function post(string $path, array|callable $handler): void
    {
        $this->add('POST', $path, $handler);
    }

    private function add(string $method, string $path, array|callable $handler): void
    {
        $pattern = '#^' . preg_replace('#\{([a-zA-Z_]+)\}#', '(?P<$1>[^/]+)', rtrim($path, '/') ?: '/') . '$#u';
        $this->routes[$method][] = ['pattern' => $pattern, 'handler' => $handler];
    }

    public function dispatch(string $method, string $uri): void
    {
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';
        // strip sub-folder base if the app is not installed at the domain root
        $base = app_base();
        if ($base && str_starts_with($path, $base)) {
            $path = substr($path, strlen($base));
        }
        // also tolerate a /public prefix (Apache setups pointing at the repo root)
        if (str_starts_with($path, '/public')) {
            $path = substr($path, 7);
        }
        $path = '/' . ltrim($path, '/');
        if ($path !== '/') {
            $path = rtrim($path, '/');
        }

        if ($method === 'POST') {
            CSRF::validate();
        }

        foreach ($this->routes[$method] ?? [] as $route) {
            if (preg_match($route['pattern'], $path, $m)) {
                $params = array_values(array_filter($m, 'is_string', ARRAY_FILTER_USE_KEY));

                $handler = $route['handler'];
                if (is_array($handler)) {
                    [$class, $action] = $handler;
                    $controller = new $class();
                    $result = $controller->{$action}(...$params);
                } else {
                    $result = $handler(...$params);
                }

                if (is_string($result) && str_starts_with($result, 'redirect:')) {
                    header('Location: ' . substr($result, 9));
                    exit;
                }

                echo $result;
                return;
            }
        }

        echo abort_page(404);
    }
}
