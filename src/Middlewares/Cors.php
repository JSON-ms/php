<?php

namespace JSONms\Middlewares;

use JSONms\Utils\Config;

class Cors implements IMiddleware {

    public static function run($all = false) {
        $accessControlAllowOrigin = Config::get('ACCESS_CONTROL_ALLOW_ORIGIN');

        // Define allowed origins for cross-origin requests
        $allowedOrigins = explode(',', $accessControlAllowOrigin);
        $origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';

        // Check if the origin is in the allowed origins and set CORS headers
        if (in_array($origin, $allowedOrigins)) {
            header("Access-Control-Allow-Origin: $origin");
        }
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, DELETE");
        header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Jms-Api-Key");
        header("Access-Control-Allow-Credentials: true");
        header('Content-Type: application/json');

        // Handle OPTIONS request
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            http_response_code(204);
            exit;
        }
    }
}
