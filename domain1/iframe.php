<?php
session_start();
require_once '../sso/app/config/config.inc.php';
require_once '../sso/app/module_sso/module_sso.php';
require_once 'app/config/config.inc.php';

$client = new Client(CFG_DOMAIN_URL);
$client->run();
$continue = $client->getContinueUrl();

echo "<h1>Domain 1</h1>";
echo '<a href="./">Home</a>';
if(isset($_SESSION['uid'])) {
    echo "<p>Logged UID: " . $_SESSION['uid'] . "</p>";
} else {
    echo "<div>";
    echo '<iframe src="'. CFG_SSO_ENDPOINT_URL .'?m=2&continue=' . $continue . '" frameborder="0"></iframe>';
    echo "</div>";
}


