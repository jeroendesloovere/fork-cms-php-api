<?php

//require
require_once '../../../autoload.php';
require_once __DIR__ . '/credentials.php';

use \ForkCms\Api\ForkCms;


// FIRST CONNECTION: getting API key so we can call functions which require authentication
// if we use a database we can save the $apiKey and skip this step the next time.

// init fork cms
$api = new ForkCms(
    $url,
    $email
);

try {
    // we get the api key for this user 
    $response = $api->coreGetAPIKey($email, $password);
//    $response = $api->coreGetInfo();

    // we need to save the api key in our database
    $apiKey = $response['api_key'];

    // we can set the api key or create a new Fork CMS Api instead.
    $api->setApiKey($apiKey);
} catch (Exception $e) {
    var_dump($e);
}

// output
var_dump($response);

// we can create a new Fork CMS API when we have the $apiKey
$api = new ForkCms(
    $url,
    $email,
    $apiKey
);

try {
// authorisation required for the following functions
//    $response = $api->coreAppleAddDevice(APPLE_DEVICE_TOKEN);
//    $response = $api->coreAppleRemoveDevice(APPLE_DEVICE_TOKEN);
//    $response = $api->blogCommentsGet();
//    $response = $api->blogCommentsGetById(40);
//    $response = $api->blogCommentsUpdate(40, null, 'FooBar');
//    $response = $api->blogCommentsUpdateStatus(array(39, 40), 'published');
} catch (Exception $e) {
    var_dump($e);
}

// output
var_dump($response);

