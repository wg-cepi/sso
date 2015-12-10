<?php
session_start();
require_once 'app/config/config.inc.php';
Database::init();

$continue = getContinue();

//echo "<pre>";
//print_r($_SERVER);
//echo "</pre>";
?>
<?php if (!isset($_COOKIE['sso_cookie']) || isset($_GET['relog'])): ?>
<form method="get">
    <label>
        Email:<input type="text" name="email"/>
    </label>
    <br>
    <label>
        Password:<input type="password" name="password"/>
    </label>
    <br>
    <input type="hidden" name="continue" value="<?php echo $continue; ?>"/>
    <input type="submit" value="Login"/>
</form>
<?php endif; ?>

<?php if (isset($_COOKIE['sso_cookie']) && !isset($_GET['relog'])): ?>
<?php $user = getUserFromCookie() ?>
<div>
    <p>You are logged in as <strong><?php echo $user['email'];?></strong></p>
    <ul>
        <li><a href="http://<?php echo CFG_JWT_ISSUER ?>/login.php?login=1&continue=<?php echo $continue;?>" title="Continue as <?php echo $user['email'] ?>"> Continue as <?php echo $user['email'];?></a></li>
        <li><a href="http://sso.local/login.php?relog=1&continue=<?php echo $continue; ?>" title="Log in as another user">Log in as another user</a>
    </ul>
</div>
<?php endif; ?>

<?php
if(!empty($_GET['email']) && !empty($_GET['password']) && isset($_GET['continue'])) {
        $aud = $_GET['continue'];
        $query = Database::$pdo->prepare("SELECT * FROM users WHERE email = ? AND password = ?");
        $query->execute(array($_GET['email'],$_GET['password']));
        $user = $query->fetch();
        if($user) {
            $hash = hash('sha256', $user['id']);
            setcookie('sso_cookie', $hash);
            $query = Database::$pdo->prepare("UPDATE users SET cookie = '" . $hash . "',logged = 1 WHERE id = " . $user["id"]);
            $query->execute();
            $token = generateJWT($user["id"], $aud);
            
            $url = getContinueUrl("http://" . $aud) . "?token=" . $token;
            redirect("http://" . $url);
        }
}

if(isset($_COOKIE['sso_cookie']) && isset($_GET['login'])) {
    $user = getUserFromCookie();
    $aud = getContinue();
    $token = generateJWT($user["id"], $aud);
    Logger::log($aud);
    $url = getContinueUrl("http://" . $aud) . "?token=" . $token;
    redirect("http://" . $url);   
}

function getUserFromCookie() {
    $cookie = $_COOKIE['sso_cookie'];
    $query = Database::$pdo->prepare("SELECT * FROM users WHERE logged = 1 AND cookie = '$cookie'");
    $query->execute();
    $user = $query->fetch();
    return ($user) ? $user : null;
}