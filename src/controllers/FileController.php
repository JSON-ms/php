<?php

namespace JSONms\Controllers;

use JSONms\Utils\ErrorHandler;

class FileController extends BaseController {

    public function listAction(string $hash) {
        $files = [];
        $listPath = $this->uploadDir . '/' . $hash . '.json';
        if (file_exists($listPath)) {
            $files = json_decode(file_get_contents($listPath));
        }

        // Return response
        http_response_code(200);
        echo json_encode($files);
        exit;
    }

    public function readAction(string $filepath): void {

        $filepath = $this->uploadDir . $filepath;

        // Set headers to force download
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($filepath) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filepath));

        // Returns the file
        ob_clean();
        flush();
        readfile($filepath);
        exit;
    }

    public function uploadAction(string $hash) {

        $file = $_FILES['file'];

        // Handle errors related to file upload
        if ($file['error'] != UPLOAD_ERR_OK) {
            switch ($file['error']) {
                case UPLOAD_ERR_INI_SIZE:
                    ErrorHandler::throwError(400, "Error: The uploaded file exceeds the maximum file size limit.");
                    break;
                case UPLOAD_ERR_FORM_SIZE:
                    ErrorHandler::throwError(400, "Error: The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.");
                    break;
                case UPLOAD_ERR_PARTIAL:
                    ErrorHandler::throwError(400, "Error: The uploaded file was only partially uploaded.");
                    break;
                case UPLOAD_ERR_NO_FILE:
                    ErrorHandler::throwError(400, "Error: No file was uploaded.");
                    break;
                case UPLOAD_ERR_NO_TMP_DIR:
                    ErrorHandler::throwError(400, "Error: Missing a temporary folder.");
                    break;
                case UPLOAD_ERR_CANT_WRITE:
                    ErrorHandler::throwError(400, "Error: Failed to write file to disk.");
                    break;
                case UPLOAD_ERR_EXTENSION:
                    ErrorHandler::throwError(400, "Error: A PHP extension stopped the file upload.");
                    break;
                default:
                    ErrorHandler::throwError(400, "Error: Unknown upload error.");
                    break;
            }
        }
        // Process the file upload if no errors
        else {
            $fileTmpPath = $file['tmp_name'];
            $fileName = $file['name'];
            $fileSize = $file['size'];
            $fileType = $file['type'];
            $extension = pathinfo($fileName, PATHINFO_EXTENSION);
            $fileHash = hash_file('md5', $fileTmpPath);

            // Specify the directory where the file will be saved
            $destPath = $this->uploadDir . $hash . '-' . $fileHash . '.' . $extension;

            // Move the uploaded file to the destination directory
            if (move_uploaded_file($fileTmpPath, $destPath)) {
                $internalPath = str_replace($this->uploadDir, '', $destPath);
                $meta = [
                    'size' => $fileSize,
                    'type' => $fileType,
                    'originalFileName' => $fileName,
                ];

                // Get image width/height
                if (str_starts_with($fileType, 'image/')) {
                    list($width, $height) = getimagesize($destPath);
                    $meta['width'] = $width;
                    $meta['height'] = $height;
                }

                // Get video width/height
                if (str_starts_with($fileType, 'video/')) {
                    try {
                        $getID3 = new \getID3();
                        $fileInfo = $getID3->analyze($destPath);
                        if (isset($fileInfo['video'])) {
                            $meta['width'] = $fileInfo['video']['width'];
                            $meta['height'] = $fileInfo['video']['height'];
                        }
                    } catch (\getid3_exception $e) {

                    }
                }

                // Update file list
                $fileList = [];
                $fileListPath = $this->uploadDir . $hash . '.json';
                if (file_exists($fileListPath)) {
                    try {
                        $fileList = json_decode(file_get_contents($fileListPath));
                        $fileList[] = [
                            'path' => $internalPath,
                            'meta' => $meta,
                        ];
                        $fileList = array_filter($fileList, function($item) use ($internalPath) {
                            return !(isset($item->path) && $item->path === $internalPath);
                        });
                        file_put_contents($fileListPath, json_encode(array_values($fileList)));
                    } catch (\Exception $e) {

                    }
                }

                // Return response
                http_response_code(200);
                echo json_encode([
                    'success' => true,
                    'publicPath' => $this->publicFilePath . $internalPath,
                    'internalPath' => $internalPath,
                    'meta' => $meta,
                ]);
                exit;
            } else {
                ErrorHandler::throwError(400, 'There was an error moving the uploaded file.');
            }
        }
    }

    public function deleteAction(string $hash, string $fileName) {
        $filePath = $this->uploadDir . $fileName;

        // Remove file if exists
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        // Remove from file list
        $fileListPath = $this->uploadDir . $hash . '.json';
        if (file_exists($fileListPath)) {
            try {
                $fileList = json_decode(file_get_contents($fileListPath));
                $fileList = array_filter($fileList, function($item) use ($fileName) {
                    return !(isset($item->path) && $item->path === $fileName);
                });
                file_put_contents($fileListPath, json_encode(array_values($fileList)));
            } catch (\Exception $e) {

            }
        }

        // Return response
        http_response_code(200);
        echo json_encode($fileList);
    }
}
