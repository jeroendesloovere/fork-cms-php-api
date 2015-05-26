<?php

// define credentials for the fork cms based website you try to connect to
$url = ''; // required, example: http://www.mydomainname.tld/api/v1/
$email = ''; // required

// calls with authentication (we use the $password to trade it in for an $apiKey)
$password = ''; // we only need to set a password if we try to call functions which require authentication
// or
$apiKey = ''; // if you know your personal apiKey, fill it in, otherwise you need to fill in the password

// url and email are required
if (empty($url) || empty($email)) {
    echo 'Please define your url and email in ' . __DIR__ . '/credentials.php';
}
