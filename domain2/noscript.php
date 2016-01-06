<?php
session_start();
require_once 'app/config/config.inc.php';

parseToken();
$continue = 'http://' . CFG_JWT_AUD . getContinuePath();
?>
<h1>Domain 2</h1>
<a href="./">Home</a>

<?php
if(!isset($_SESSION['uid'])){
    echo '<form method="get" action="' . CFG_AUTH_SERVER_ENDPOINT . '">
        <input type="hidden" name="continue" value="' . $continue . '"/>
        <input type="hidden" name="m" value="1"/>
        <input type="submit" value="Login with SSO"/>
    </form>';
} else {
    echo "<p>Logged UID: " . $_SESSION['uid'] . "</p>";
}



