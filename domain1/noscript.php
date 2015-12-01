<?php
session_start();
require_once 'app/config/config.inc.php';

parseToken();
$continue = CFG_JWT_AUD . getContinuePath();
?>
<h1>Domain 1</h2>
<a href="./">Home</a>

<?php if(!isset($_SESSION['uid'])): ?>
<form method="get" action="http://<?php echo CFG_AUTH_SERVER ?>/login.php">
    <input type="hidden" name="continue" value="<?php echo $continue;?>"/>
    <input type="submit" value="Login with SSO"/>
</form>
<?php endif; ?>
<?php
if(isset($_SESSION['uid'])) {
    echo "<p>Logged UID: " . $_SESSION['uid'] . "</p>";
}



