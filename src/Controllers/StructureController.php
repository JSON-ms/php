<?php

namespace JSONms\Controllers;

class StructureController extends BaseController {

    public function saveAction(string $hash, $structure) {
        $structureFilePath = $this->structurePath . $hash . '.json';

        if (file_exists($structureFilePath)) {
            $timestamp = filemtime($structureFilePath);
            copy($structureFilePath, $this->structureHistoryPath . $hash . '.' . $timestamp . '.json');
        }

        file_put_contents($structureFilePath, json_encode($structure));

        http_response_code(200);
        echo json_encode($structure);
        exit;
    }
}
