<?php

// Define ANSI color codes
define('COLOR_GREEN', "\033[32m");
define('COLOR_WHITE', "\033[0m");

// Function to prompt a question and get user input
function prompt($question, $default = null) {
    echo COLOR_GREEN . $question . COLOR_WHITE . ": ";
    $handle = fopen("php://stdin", "r");
    $response = trim(fgets($handle));
    fclose($handle);
    return empty($response) ? $default : $response;
}

$createFile = prompt('Would you like to automatically create an index.php that instantiate JSON.ms? (Y) ', 'Y');
if (strtoupper($createFile) == 'Y') {
    $privatePath = preg_replace('/(\/|\\\)+/', DIRECTORY_SEPARATOR , getcwd() . '/private/');
    $privatePath = prompt('Where in your file system do you want to save your data? (default: ' . $privatePath . ') ', $privatePath);
    $privatePath = str_replace('\\', '\\\\', $privatePath);

    $publicUrl = 'http://localhost:8080';
    $publicUrl = prompt('What will be your public endpoint URL? (default: ' . $publicUrl . ') ', $publicUrl);

    $acao = 'https://json.ms';
    $acao = prompt('Define the Access-Control-Allow-Origin (default: ' . $acao . ') ', $acao);

    $secretKey = prompt('Your endpoint secret key? ');
    $cypherKey = prompt('Your endpoint cypher key? ');

    $fileContent = <<<EOD
<?php

use JSONms\JSONms;

require 'vendor/autoload.php';

// Load JSONms configurations
\$jsonms = new JSONms(
    '$privatePath', // Where to read/save your data in your file system?
    '$publicUrl', // Public path of your server endpoint
    '$acao', // Set to "https://json.ms" if you do not need your own instance of JSON.ms. You can add multiple URLs by seperating them by a comma.
    '$secretKey', // Obtain from your endpoint in Settings panel in Advanced mode.
    '$cypherKey', // Obtain from your endpoint in Settings panel in Advanced mode.
);

// Handle errors (if required) and requests
\$jsonms->handleErrors(); // Optional. Remove if you prefer to handle errors yourself.
\$jsonms->handleRequests(); // You can pass an URI param. (ex: /data/get/YOUR_HASH)`);

EOD;

    file_put_contents(getcwd() . '/index.php', $fileContent);

    echo COLOR_GREEN . 'A new index.php file has been created!' . COLOR_WHITE . "\n";
}
