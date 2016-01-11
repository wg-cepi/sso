<?php
session_start();
require_once '../sso/app/config/config.inc.php';
require_once '../sso/app/module_sso/module_sso.php';

require_once 'app/config/config.inc.php';

$client = new Client(CFG_DOMAIN_URL);
$client->run();

echo "<h1>Domain 1</h1>";
echo '<a href="./logout.php">Local logout</a>';
echo '<a href="./cross.php">Login Crossroads</a>';

if(isset($_SESSION['uid'])) {
    echo "<p>Logged UID: " . $_SESSION['uid'] . "</p>";
} else {
    echo '<noscript>
            <meta http-equiv="refresh" content="0;url=noscript.php">
        </noscript>';
    $client->pickLoginMethod();
}
