<?php

namespace JSONms\Controllers;

use JSONms\Utils\Config;

abstract class BaseController {

    protected string $privatePath;
    protected string $publicFilePath;
    protected string $dataPath;
    protected string $dataHistoryPath;
    protected string $interfacePath;
    protected string $interfaceHistoryPath;
    protected string $uploadDir;

    public function __construct() {
        $this->privatePath = Config::get('PRIVATE_FILE_PATH');
        $this->publicFilePath = Config::get('PUBLIC_FILE_PATH') . '/file/get';
        $this->dataPath = $this->privatePath . '/data/';
        $this->dataHistoryPath = $this->privatePath . '/data/history/';
        $this->interfacePath = $this->privatePath . '/interfaces/';
        $this->interfaceHistoryPath = $this->privatePath . '/interfaces/history/';
        $this->uploadDir = $this->privatePath . '/files/';

        // Create directories if they do not exist
        if (!is_dir($this->interfacePath)) {
            mkdir($this->interfacePath, 0755, true);
        }
        if (!is_dir($this->dataPath)) {
            mkdir($this->dataPath, 0755, true);
        }
        if (!is_dir($this->dataHistoryPath)) {
            mkdir($this->dataHistoryPath, 0755, true);
        }
        if (!is_dir($this->interfaceHistoryPath)) {
            mkdir($this->interfaceHistoryPath, 0755, true);
        }
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }
    }
}
