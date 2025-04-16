<?php

namespace JSONms\Controllers;

use JSONms\Utils\Config;

abstract class BaseController {

    protected string $privatePath;
    protected string $publicUrl;
    protected string $dataPath;
    protected string $dataHistoryPath;
    protected string $structurePath;
    protected string $structureHistoryPath;
    protected string $uploadDir;

    public function __construct() {
        $this->privatePath = Config::get('PRIVATE_FILE_PATH');
        $this->publicUrl = Config::get('PUBLIC_URL') . '/file/read/';
        $this->dataPath = $this->privatePath . '/data/';
        $this->dataHistoryPath = $this->privatePath . '/data/history/';
        $this->structurePath = $this->privatePath . '/structures/';
        $this->structureHistoryPath = $this->privatePath . '/structures/history/';
        $this->uploadDir = $this->privatePath . '/files/';

        $this->privatePath = preg_replace('/(\/|\\\)+/', DIRECTORY_SEPARATOR , $this->privatePath);
        $this->dataPath = preg_replace('/(\/|\\\)+/', DIRECTORY_SEPARATOR , $this->dataPath);
        $this->dataHistoryPath = preg_replace('/(\/|\\\)+/', DIRECTORY_SEPARATOR , $this->dataHistoryPath);
        $this->structurePath = preg_replace('/(\/|\\\)+/', DIRECTORY_SEPARATOR , $this->structurePath);
        $this->structureHistoryPath = preg_replace('/(\/|\\\)+/', DIRECTORY_SEPARATOR , $this->structureHistoryPath);
        $this->uploadDir = preg_replace('/(\/|\\\)+/', DIRECTORY_SEPARATOR , $this->uploadDir);

        // Create directories if they do not exist
        if (!is_dir($this->structurePath)) {
            mkdir($this->structurePath, 0755, true);
        }
        if (!is_dir($this->dataPath)) {
            mkdir($this->dataPath, 0755, true);
        }
        if (!is_dir($this->dataHistoryPath)) {
            mkdir($this->dataHistoryPath, 0755, true);
        }
        if (!is_dir($this->structureHistoryPath)) {
            mkdir($this->structureHistoryPath, 0755, true);
        }
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }
    }

    protected function updatestructure(string $hash, $structure) {
        $structureFilePath = $this->structurePath . $hash . '.json';

        if (file_exists($structureFilePath)) {
            $timestamp = filemtime($structureFilePath);
            copy($structureFilePath, $this->structureHistoryPath . $hash . '.' . $timestamp . '.json');
        }

        file_put_contents($structureFilePath, json_encode($structure));
    }
}
