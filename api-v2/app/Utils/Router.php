<?php
namespace App\Utils;

class Router
{
    private array $routes = [];


    public function __construct(?string $routesFile = null)
    {
        if ($routesFile && file_exists($routesFile)) {
            $this->routes = require $routesFile;
        }
    }


    public function dispatch(): void
    {
        $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        $scriptName = $_SERVER['SCRIPT_NAME'];
        $basePath = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');
        if (strpos($uri, $basePath) === 0) {
            $uri = substr($uri, strlen($basePath));
        }
        $uri = '/' . trim($uri, '/');
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';


        foreach ($this->routes as $route) {
            if (count($route) < 4) {
                continue;
            }

            [$routeMethod, $pattern, $controllerClass, $actionMethod] = $route;

            if ($routeMethod !== $method) {
                continue;
            }

            $regex = '#^' . preg_replace('#\{([a-zA-Z0-9_]+)\}#', '(?P<$1>[^/]+)', $pattern) . '$#';

            if (preg_match($regex, $uri, $matches)) {
                if (!class_exists($controllerClass)) {
                    http_response_code(Response::HTTP_NOT_FOUND);
                    echo "Controller not found: $controllerClass";
                    return;
                }


                $instance = new $controllerClass();
                if (!method_exists($instance, $actionMethod)) {
                    http_response_code(Response::HTTP_NOT_FOUND);
                    echo "Method not found: $actionMethod";
                    return;
                }


                $params = array_filter(
                    $matches,
                    fn($k) => !is_int($k),
                    ARRAY_FILTER_USE_KEY
                );


                try {
                    call_user_func_array([$instance, $actionMethod], $params);
                } catch (\Throwable $exception) {
                    http_response_code(Response::HTTP_INTERNAL_SERVER_ERROR);
                    echo json_encode([
                        'error' => 'Error: ' . $exception->getMessage(),
                        'line' => $exception->getLine(),
                        'code' => $exception->getCode(),
                        'trace' => $exception->getTrace()
                    ]);
                }
                return;
            }
        }


        http_response_code(Response::HTTP_NOT_FOUND);
        echo json_encode(['error' => 'Not Found']);
    }
}