<?php
session_start();
require_once __DIR__ . '/vendor/autoload.php';

echo "<h1>SSO here</h1>";

/*
 ***********
 * Facebook *
 ***********
 */
echo "<h2>Facebook</h2>";
const FB_APP_ID = '1707595419474201';
const FB_APP_SECRET = '45b996d90b59818ee53d033781ea8be5';

$fb = new Facebook\Facebook([
  'app_id' => FB_APP_ID,
  'app_secret' => FB_APP_SECRET,
  'default_graph_version' => 'v2.2',
  ]);
if(!isset($_SESSION['facebook_access_token'])){
    $helper = $fb->getRedirectLoginHelper();
    $permissions = ['email', 'user_likes']; // optional
    $loginUrl = $helper->getLoginUrl('http://sso.local/facebookLogin.php', $permissions);
    echo '<a href="' . $loginUrl . '">Log in with Facebook</a>';
} else{
    $fb->setDefaultAccessToken($_SESSION['facebook_access_token']);

    try {
      $response = $fb->get('/me?fields=email');
      $userNode = $response->getGraphUser();
    } catch(Facebook\Exceptions\FacebookResponseException $e) {
      // When Graph returns an error
      echo 'Graph returned an error: ' . $e->getMessage();
      exit;
    } catch(Facebook\Exceptions\FacebookSDKException $e) {
      // When validation fails or other local issues
      echo 'Facebook SDK returned an error: ' . $e->getMessage();
      exit;
    }
    echo '<p>Email: ' . $userNode->getEmail() . "</p>";
}

/*
 ***********
 * Google *
 ***********
 */
echo "<h2>Google</h2>";
const CLIENT_ID = '851847042686-3n51ffn8p9jhnc83e50fve62tc7uaokq.apps.googleusercontent.com';
const CLIENT_SECRET = 'd5izoJR_c9o00xV9zmfOvYcx';
const REDIRECT_URI = 'http://localhost/googleLogin.php';

$client_id = CLIENT_ID;
$client_secret = CLIENT_SECRET ;
$redirect_uri = REDIRECT_URI;

$client = new Google_Client();
$client->setClientId($client_id);
$client->setClientSecret($client_secret);
$client->setRedirectUri($redirect_uri);
$client->setScopes('email');

if(!isset($_SESSION['google_auth_token']) && !isset($_SESSION['google_access_token'])) {
    $authUrl = $client->createAuthUrl();

    echo '<a href="' . $authUrl . '">Login with Google</a>';

} else if(isset($_SESSION['google_access_token']) && $_SESSION['google_access_token']) {
    $client->setAccessToken($_SESSION['google_access_token']);
    
    if ($client->getAccessToken()) {
        $_SESSION['google_access_token'] = $client->getAccessToken();
        $token_data = $client->verifyIdToken()->getAttributes();
        echo "<p>Email: " . $token_data['payload']['email'] . "</p>";
    }

}
/*
 ***********
 * TWITTER *
 ***********
 */
use Abraham\TwitterOAuth\TwitterOAuth;

echo "<h2>Twitter</h2>";
const CONSUMER_KEY = 'A4iOMp7m49jBrhZV3KjRz3cUm';
const CONSUMER_SECRET = 'vTPLq11i7VDmbDstIGoa7SrJC1LIlKnmkTlujlzjnW1RZ2Irmt';
const OAUTH_CALLBACK = 'http://sso.local/twitterLogin.php';

if(!isset($_SESSION['twitter_access_token'])) {
    $connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET);
    $request_token = $connection->oauth('oauth/request_token', array('oauth_callback' => OAUTH_CALLBACK));

    $_SESSION['twitter_oauth_token'] = $request_token['oauth_token'];
    $_SESSION['twitter_oauth_token_secret'] = $request_token['oauth_token_secret'];

    $url = $connection->url('oauth/authorize', array('oauth_token' => $request_token['oauth_token']));
    echo '<a href="' . $url . '"> Login with Twitter</a>';
} else {
    $access_token = $_SESSION['twitter_access_token'];
    $connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $access_token['oauth_token'], $access_token['oauth_token_secret']);
    $user = $connection->get("account/verify_credentials");
    echo "<ul>";
    echo "<li>ID: " . $user->id . "</li>";
    echo "<li>Email: " . $user->name . "</li>";
    echo "</ul>";
}
echo "<hr/>";
echo "<a href='./logout.php'>Logout</a>";
