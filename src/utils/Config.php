<?php

namespace JSONms\Utils;

class Config {
    private static $config = [];

    public static function set(string $key, string $value) {
        self::$config[$key] = $value;
    }

    public static function get(string $key) {
        return self::$config[$key] ?? null;
    }
}
