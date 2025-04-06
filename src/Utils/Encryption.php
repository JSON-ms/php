<?php

namespace JSONms\Utils;

class Encryption {

    public static function decrypt($encryptedData, $encryptionKey): string {
        $arr = explode('::', base64_decode($encryptedData), 2);
        if (count($arr) != 2) {
            return false;
        }
        list($encryptedData, $iv) = $arr;
        return openssl_decrypt($encryptedData, 'AES-256-CBC', $encryptionKey, 0, $iv);
    }
}
