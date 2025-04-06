<?php

namespace JSONms\Middlewares;

use JSONms\Utils\Config;
use JSONms\Utils\ErrorHandler;
use JSONms\Utils\Encryption;

class Secret implements IMiddleware {

    public static function run() {

        $secretKey = Config::get('SECRET_KEY');
        $cypherKey = Config::get('CYPHER_KEY');

        // Get the headers from the request
        $headers = getallheaders();

        // Validate if the API Key is provided and correct
        if (!isset($headers['X-Jms-Api-Key'])) {
            ErrorHandler::throwError(401, 'API Secret Key not provided');
        } elseif (Encryption::decrypt($headers['X-Jms-Api-Key'], $cypherKey) !== $secretKey) {
            ErrorHandler::throwError(401, 'Invalid API Secret Key');
        }
    }
}
