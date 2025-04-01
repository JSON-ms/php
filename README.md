# @jsonms/php

A PHP request handler to use as a endpoint and configure as a webhook in [json.ms](https://json.ms).

## Installation

You can install `@jsonms/php` via composer:

```sh
composer install jsonms/php
```

### Usage
```php
<?php

use JSONms\JSONms;

require 'vendor/autoload.php';

// Load JSONms configurations
$jsonms = new JSONms(
    'YOUR_PRIVATE_FILE_PATH',
    'YOUR_PUBLIC_FILE_PATH',
    'YOUR_ACCESS_CONTROL_ALLOW_ORIGIN',
    'YOUR_SECRET_KEY',
    'YOUR_CYPHER_KEY',
);

// Handle errors, middlewares and requests
$jsonms->handleErrors(); // Optional. Remove if you prefer to handle errors yourself.
$jsonms->handleRequests(); // You can pass an URI param. (ex: /data/get/YOUR_HASH)
```
