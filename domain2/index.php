<?php
session_start();
require_once 'app/config/config.inc.php';
parseToken();

echo "<h1>Domain 2</h1>";
if(isset($_SESSION['uid'])) {
    echo "<p>Logged UID: " . $_SESSION['uid'] . "</p>";
}
?>

<ul>
    <li><a href="./iframe.php">Iframe method</a></li>
    <li><a href="./cors.php">AJAX + CORS method</a></li>
    <li><a href="./noscript.php">Noscript method</a></li>
    <li><a href="./logout.php">Local logout</a></li>
</ul>
