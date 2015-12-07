<?php
session_start();
require_once __DIR__ . '/vendor/autoload.php';
//print_r($_SESSION);

const APP_ID = '1707595419474201';
const APP_SECRET = '45b996d90b59818ee53d033781ea8be5';

$fb = new Facebook\Facebook([
  'app_id' => APP_ID,
  'app_secret' => APP_SECRET,
  'default_graph_version' => 'v2.2',
  ]);


echo "<h1>SSO here</h1>";
if(!isset($_SESSION['facebook_access_token'])){
    $helper = $fb->getRedirectLoginHelper();
    $permissions = ['email', 'user_likes']; // optional
    $loginUrl = $helper->getLoginUrl('http://sso.local/facebookLogin.php', $permissions);
    echo '<a href="' . $loginUrl . '">Log in with Facebook!</a>';
} else{
    $fb->setDefaultAccessToken($_SESSION['facebook_access_token']);

    try {
      $response = $fb->get('/me');
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
    echo 'Logged in as ' . $userNode->getName();
}


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

/************************************************
  If we're logging out we just need to clear our
  local access token in this case
 ************************************************/
if (isset($_REQUEST['logout'])) {
  unset($_SESSION['access_token']);
}

/************************************************
  If we have a code back from the OAuth 2.0 flow,
  we need to exchange that with the authenticate()
  function. We store the resultant access token
  bundle in the session, and redirect to ourself.
 ************************************************/
if (isset($_GET['code'])) {
  $client->authenticate($_GET['code']);
  $_SESSION['access_token'] = $client->getAccessToken();
  $redirect = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
  header('Location: ' . filter_var($redirect, FILTER_SANITIZE_URL));
}

/************************************************
  If we have an access token, we can make
  requests, else we generate an authentication URL.
 ************************************************/
if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
  $client->setAccessToken($_SESSION['access_token']);
} else {
  $authUrl = $client->createAuthUrl();
}

/************************************************
  If we're signed in we can go ahead and retrieve
  the ID token, which is part of the bundle of
  data that is exchange in the authenticate step
  - we only need to do a network call if we have
  to retrieve the Google certificate to verify it,
  and that can be cached.
 ************************************************/
if ($client->getAccessToken()) {
  $_SESSION['access_token'] = $client->getAccessToken();
  $token_data = $client->verifyIdToken()->getAttributes();
}

if (
    $client_id == CLIENT_ID
    || $client_secret == CLIENT_SECRET 
    || $redirect_uri == REDIRECT_URI) {
  //echo missingClientSecretsWarning();
}
?>
<div class="box">
  <div class="request">
    <?php if (isset($authUrl)): ?>
      <a class='login' href='<?php echo $authUrl; ?>'>Connect Me!</a>
    <?php else: ?>
      <a class='logout' href='?logout'>Logout</a>
    <?php endif ?>
  </div>

  <?php if (isset($token_data)): ?>
    <div class="data">
      <?php var_dump($token_data); ?>
    </div>
  <?php endif ?>
</div>
<?php

/*
 ***********
 * TWITTER *
 ***********
 */
use Abraham\TwitterOAuth\TwitterOAuth;
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
    echo "<h3>Twitter</h3>";
    echo "<ul>";
    echo "<li>ID: " . $user->id . "</li>";
    echo "<li>Email: " . $user->name . "</li>";
    echo "</ul>";
}
echo "<hr/>";
echo "<a href='./logout.php'>Logout</a>";
