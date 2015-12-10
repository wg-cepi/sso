<?php
session_start();
require_once 'app/config/config.inc.php';
require_once __DIR__ . '/vendor/autoload.php';

const CLIENT_ID = '851847042686-3n51ffn8p9jhnc83e50fve62tc7uaokq.apps.googleusercontent.com';
const CLIENT_SECRET = 'd5izoJR_c9o00xV9zmfOvYcx';
const REDIRECT_URI = 'http://localhost/googleLogin.php';

if (isset($_GET['code'])) {
    $client_id = CLIENT_ID;
    $client_secret = CLIENT_SECRET ;
    $redirect_uri = REDIRECT_URI;

    $client = new Google_Client();
    $client->setClientId($client_id);
    $client->setClientSecret($client_secret);
    $client->setRedirectUri($redirect_uri);
    $client->setScopes('email');
    $client->authenticate($_GET['code']);
    $_SESSION['google_access_token'] = $client->getAccessToken();
    //$redirect = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
    $redirect = 'http://sso.local';
    header('Location: ' . filter_var($redirect, FILTER_SANITIZE_URL));
}
