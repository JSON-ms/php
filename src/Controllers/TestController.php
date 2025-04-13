<?php

namespace JSONms\Controllers;

class TestController extends BaseController {

    public function tryAction(): void {
        http_response_code(200);
        echo json_encode(true);
        exit;
    }
}
