<?php

namespace JSONms\Utils;

class Composer {

    public static function getVersion() {

        $ds = DIRECTORY_SEPARATOR;
        $filePath = realpath(dirname(__FILE__) . $ds . '..' . $ds . '..' . $ds . 'composer.json');

        if (!file_exists($filePath)) {
            return null;
        }

        $jsonContent = file_get_contents($filePath);
        $composerData = json_decode($jsonContent, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Error decoding JSON: " . json_last_error_msg());
        }

        return isset($composerData['version']) ? $composerData['version'] : null;
    }
}
