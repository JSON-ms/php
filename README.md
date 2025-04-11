# jsonms/php

A PHP request handler to use as a endpoint and configure as a webhook in [json.ms](https://json.ms).

## Installation

You can install `jsonms/php` via composer:

```sh
composer require jsonms/php
```

### Usage
```php
<?php

use JSONms\JSONms;

require 'vendor/autoload.php';

// Load JSONms configurations
$jsonms = new JSONms(
    'PRIVATE_DATA_PATH', // Where to read/save your data in your file system?
    'PUBLIC_URL', // Public path of your server (webhook)
    'ACCESS_CONTROL_ALLOW_ORIGIN', // Set to "https://json.ms" if you do not need your own instance of JSON.ms. You can add multiple URLs by seperating them by a comma.
    'SECRET_KEY', // Obtain from your Webhook Endpoint in Settings panel in Advanced mode.
    'CYPHER_KEY', // Obtain from your Webhook Endpoint in Settings panel in Advanced mode.
);

// Handle errors (if required) and requests
$jsonms->handleErrors(); // Optional. Remove if you prefer to handle errors yourself.
$jsonms->handleRequests(); // You can pass an URI param. (ex: /data/get/YOUR_HASH)
```
