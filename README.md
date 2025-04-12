# jsonms/php

A PHP request handler to use as a endpoint and configure as a webhook in [json.ms](https://json.ms).

## Installation

You can install `jsonms/php` via composer:

### Requirements

```sh
composer require jsonms/php
```

### Preparation

Make sure you first created a webhook in the JSON.ms Settings section of your interface. Obtain your secret and cypher key using the Get button of each field.

### Configuration

For auto-configuration, you can launch the install script.

```bash
php vendor/jsonms/php/install.php
```

Or create a index.php file manually in your directory.

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

## Usage

To test locally, you can start a PHP built-in Web server:

```bash
php -S localhost:8080 index.php
```

Now you can read, save, upload and delete data from your server with any project bound to the webhook you configured as long as this server is running!