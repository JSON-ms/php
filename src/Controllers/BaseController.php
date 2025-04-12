<?php

namespace JSONms\Controllers;

use JSONms\Utils\Config;

abstract class BaseController {

    protected string $privatePath;
    protected string $publicUrl;
    protected string $dataPath;
    protected string $dataHistoryPath;
    protected string $interfacePath;
    protected string $interfaceHistoryPath;
    protected string $uploadDir;

    public function __construct() {
        $this->privatePath = Config::get('PRIVATE_FILE_PATH');
        $this->publicUrl = Config::get('PUBLIC_URL') . '/file/read/';
        $this->dataPath = $this->privatePath . '/data/';
        $this->dataHistoryPath = $this->privatePath . '/data/history/';
        $this->interfacePath = $this->privatePath . '/interfaces/';
        $this->interfaceHistoryPath = $this->privatePath . '/interfaces/history/';
        $this->uploadDir = $this->privatePath . '/files/';

        $this->privatePath = preg_replace('/(\/|\\\)+/', DIRECTORY_SEPARATOR , $this->privatePath);
        $this->dataPath = preg_replace('/(\/|\\\)+/', DIRECTORY_SEPARATOR , $this->dataPath);
        $this->dataHistoryPath = preg_replace('/(\/|\\\)+/', DIRECTORY_SEPARATOR , $this->dataHistoryPath);
        $this->interfacePath = preg_replace('/(\/|\\\)+/', DIRECTORY_SEPARATOR , $this->interfacePath);
        $this->interfaceHistoryPath = preg_replace('/(\/|\\\)+/', DIRECTORY_SEPARATOR , $this->interfaceHistoryPath);
        $this->uploadDir = preg_replace('/(\/|\\\)+/', DIRECTORY_SEPARATOR , $this->uploadDir);

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
