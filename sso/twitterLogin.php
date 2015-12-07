<?php
session_start();
require_once 'app/config/config.inc.php';
require_once __DIR__ . '/vendor/autoload.php';

use Abraham\TwitterOAuth\TwitterOAuth;
const CONSUMER_KEY = 'A4iOMp7m49jBrhZV3KjRz3cUm';
const CONSUMER_SECRET = 'vTPLq11i7VDmbDstIGoa7SrJC1LIlKnmkTlujlzjnW1RZ2Irmt';
const OAUTH_CALLBACK = 'http://sso.local/twitterLogin.php';

$request_token = [];
$request_token['oauth_token'] = $_SESSION['twitter_oauth_token'];
$request_token['oauth_token_secret'] = $_SESSION['twitter_oauth_token_secret'];

if (isset($_REQUEST['oauth_token']) && $request_token['oauth_token'] !== $_REQUEST['oauth_token']) {
    // Abort! Something is wrong.
} else {
    $connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $request_token['oauth_token'], $request_token['oauth_token_secret']);
    $access_token = $connection->oauth("oauth/access_token", array("oauth_verifier" => $_REQUEST['oauth_verifier']));
    $_SESSION['twitter_access_token'] = $access_token;
    redirect('sso.local');
}