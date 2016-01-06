<?php
session_start();
require_once 'app/config/config.inc.php';

$continue = 'http://' .  CFG_JWT_AUD . getContinuePath();
parseToken();

echo "<h1>Domain 1</h1>";
echo '<a href="./">Home</a>';
if(isset($_SESSION['uid'])) {
    echo "<p>Logged UID: " . $_SESSION['uid'] . "</p>";
} else {
    echo "<div>";
    echo '<iframe src="'. CFG_AUTH_SERVER_ENDPOINT .'?m=2&continue=' . $continue . '" frameborder="0"></iframe>';
    echo "</div>";
}


