<?php
session_start();
require_once '../sso/app/config/config.inc.php';
require_once '../sso/app/module_sso/module_sso.php';
require_once 'app/config/config.inc.php';

$client = new Client(CFG_DOMAIN_URL);
$client->run();
$continue = $client->getContinueUrl();
?>
<h1>Domain 2</h1>
<a href="./">Home</a>

<?php if(!isset($_SESSION['uid'])): ?>
<form method="get" action="<?php echo CFG_SSO_ENDPOINT_URL ?>">
    <input type="hidden" name="continue" value="<?php echo $continue;?>"/>
    <input type="hidden" name="m" value="1"/>
    <input type="submit" value="Login with SSO"/>
</form>
<?php endif; ?>
<?php
if(isset($_SESSION['uid'])) {
    echo "<p>Logged UID: " . $_SESSION['uid'] . "</p>";
}



