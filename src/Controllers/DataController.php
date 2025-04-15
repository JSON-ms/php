<?php

namespace JSONms\Controllers;

use JSONms\Utils\ErrorHandler;
use JSONms\Utils\Composer;

class DataController extends BaseController {

    public function getAction(string $hash): void {
        http_response_code(200);
        $dataFilePath = $this->dataPath . $hash . '.json';
        $interfaceFilePath = $this->interfacePath . $hash . '.json';
        $data = [];
        $interface = [];
        if (file_exists($dataFilePath)) {
            $data = json_decode(file_get_contents($dataFilePath));
        }
        if (file_exists($interfaceFilePath)) {
            $interface = json_decode(file_get_contents($interfaceFilePath));
        }

        // Do not cache the data
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");

        echo json_encode([
            'data' => $data,
            'interface' => $interface,
            'settings' => [
                "uploadMaxSize" => ini_get('upload_max_filesize'),
                "postMaxSize" => ini_get('post_max_size'),
                'publicUrl' => $this->publicUrl,
                'version' => Composer::getVersion(),
                'supportedFeatures' => [
                    'data/get',
                    'data/update',
                    'file/list',
                    'file/read',
                    'file/upload',
                    'file/delete',
                ],
            ],
        ]);
        exit;
    }

    public function updateAction(string $hash, \stdClass $data): void {
        $dataFilePath = $this->dataPath . $hash . '.json';
        $interfaceFilePath = $this->interfacePath . $hash . '.json';

        if (json_last_error() !== JSON_ERROR_NONE) {
            ErrorHandler::throwError(400, 'Invalid JSON');
        }

        // Save to data and interface history if files already exist
        if (file_exists($dataFilePath)) {
            $timestamp = filemtime($dataFilePath);
            copy($dataFilePath, $this->dataHistoryPath . $hash . '.' . $timestamp . '.json');
        }
        if (file_exists($interfaceFilePath)) {
            $timestamp = filemtime($interfaceFilePath);
            copy($interfaceFilePath, $this->interfaceHistoryPath . $hash . '.' . $timestamp . '.json');
        }

        // Save the data and interface to JSON files
        file_put_contents($dataFilePath, json_encode($data->data));
        file_put_contents($interfaceFilePath, json_encode($data->interface));
        http_response_code(200);
        echo json_encode($data);
        exit;
    }

    public function historyAction(string $hash, string $fromDate, string $toDate): void {

    }
}
