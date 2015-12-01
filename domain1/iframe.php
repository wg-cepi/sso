<?php
session_start();
require_once 'app/config/config.inc.php';

$continue =  CFG_JWT_AUD . getContinuePath();
parseToken();

echo "<h1>Domain 1</h1>";
echo '<a href="./">Home</a>';
if(isset($_SESSION['uid'])) {
    echo "<p>Logged UID: " . $_SESSION['uid'] . "</p>";
} else {
    echo "<div>";
    echo '<iframe src="http://'. CFG_AUTH_SERVER .'/jwt.php?continue=' . $continue . '" frameborder="0"></iframe>';
    echo "</div>";
}


