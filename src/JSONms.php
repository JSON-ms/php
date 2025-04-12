<?php

namespace JSONms;

use JSONms\Utils\Config;
use JSONms\Middlewares\Cors;
use JSONms\Middlewares\Secret;
use JSONms\Utils\ErrorHandler;

class JSONms {

    public function __construct(
        string $privatePath,
        string $publicUrl,
        string $accessControlAllowOrigin,
        string $secretKey,
        string $cypherKey,
    ) {
        Config::set('PRIVATE_FILE_PATH', $privatePath);
        Config::set('PUBLIC_URL', $publicUrl);
        Config::set('ACCESS_CONTROL_ALLOW_ORIGIN', $accessControlAllowOrigin);
        Config::set('SECRET_KEY', $secretKey);
        Config::set('CYPHER_KEY', $cypherKey);
    }

    public function handleErrors() {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        set_error_handler(['JSONms\Utils\ErrorHandler', 'customErrorHandler']);
        set_exception_handler(['JSONms\Utils\ErrorHandler', 'customExceptionHandler']);
        register_shutdown_function(['JSONms\Utils\ErrorHandler', 'shutdownFunction']);
    }

    public function handleMiddlewares() {
        Cors::run();
        Secret::run();
    }

    public function handleRequests($uri = null) {
        if ($uri === null) {
            $uri = $_SERVER['REQUEST_URI'];
        }

        $requestPath = parse_url($uri);
        $requestPath = trim($requestPath['path'], '/');
        $splitRequestUri = explode('/', $requestPath);

        $controllerName = ucfirst($splitRequestUri[0]) . 'Controller';
        $controllerNameAndSpace = '\\JSONms\\Controllers\\' . ucfirst($splitRequestUri[0]) . 'Controller';
        $srcPath = __DIR__ . '/Controllers/' . $controllerName . '.php';

        // Check if the requested script exists
        if (file_exists($srcPath)) {
            try {
                $controller = new $controllerNameAndSpace();
                $actionName = null;
                $params = [];

                if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                    $actionName = count($splitRequestUri) < 2 ? 'index' : 'get';
                } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $actionName = count($splitRequestUri) === 2 ? 'update' : 'create';
                } elseif ($_SERVER['REQUEST_METHOD'] === 'PUT' && count($splitRequestUri) === 2) {
                    $actionName = 'update';
                } elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
                    $actionName = 'delete';
                }

                for ($i = 1; $i < count($splitRequestUri); $i++) {
                    $words = explode('-', $splitRequestUri[$i]);
                    $camelCase = strtolower(array_shift($words));
                    foreach ($words as $word) {
                        $camelCase .= ucfirst($word);
                    }

                    if ($i === 1 && method_exists($controller, $camelCase . 'Action')) {
                        $actionName = $camelCase;
                    } elseif (count($splitRequestUri) === 2) {
                        $params = [$splitRequestUri[1]];
                    } elseif ($i > 1) {
                        $params = array_slice($splitRequestUri, 2);
                    }
                }

                Cors::run();
                if (
                    !($controllerName == 'FileController' && $actionName == 'read')
                    || (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
                ) {
                    Secret::run();
                }

                if ($_SERVER['REQUEST_METHOD'] === 'POST' && count($_FILES) === 0) {
                    $json = file_get_contents('php://input');
                    $data = json_decode($json);

                    if (json_last_error() !== JSON_ERROR_NONE) {
                        ErrorHandler::throwError(400, 'Invalid JSON');
                    }
                    $params[] = $data;
                }

                $reflection = new \ReflectionMethod($controllerNameAndSpace, $actionName . 'Action');
                if (count($params) != $reflection->getNumberOfParameters()) {
                    ErrorHandler::throwError(400, 'Invalid API call');
                }

                $controller->{$actionName . 'Action'}(...$params);
            } catch (\Exception $e) {
                $this->handleMiddlewares();
                http_response_code($e->getCode());
                ErrorHandler::throwError(500, $e->getMessage());
                exit;
            }
        } else {
            $this->handleMiddlewares();
            ErrorHandler::throwError(404, "404 Not Found");
        }
    }
}
