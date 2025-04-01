<?php

namespace JSONms\Utils;

class ErrorHandler {

    public static function throwError($code, $body) {
        http_response_code($code || 500);
        echo json_encode(['body' => $body]);
        exit;
    }

    public static function customErrorHandler($errno, $errstr, $errfile, $errline) {
        http_response_code(500);
        echo json_encode(['body' => "[$errno]: $errstr in $errfile on line $errline"]);
        exit();
    }

    public static function customExceptionHandler($exception) {
        http_response_code(500);
        echo json_encode(['body' => $exception->getMessage()]);
        exit();
    }

    public static function shutdownFunction() {
        $error = error_get_last();
        if ($error) {
            http_response_code(500);
            echo json_encode(['body' => $error['message']]);
            exit();
        }
    }
}
