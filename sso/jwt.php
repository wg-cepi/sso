<?php
require_once 'app/config/config.inc.php';

function login() {
    $continue = isset($_GET['continue']) ? $_GET['continue'] : "";
    //print_r($_POST);
    if(!empty($_GET['email']) && !empty($_GET['password'])) {
        $query = Database::$pdo->prepare("SELECT * FROM users WHERE email = ? AND password = ?");
        $query->execute(array($_GET['email'],$_GET['password']));
        $user = $query->fetch();
        if($user) {
            $hash = hash('sha256', $user['id']);
            setcookie('sso_cookie', $hash);
            $query = Database::$pdo->prepare("UPDATE users SET cookie = '" . $hash . "',logged = 1 WHERE id = " . $user["id"]);
            $query->execute();
            $aud = $continue;
            $token = generateJWT($user["id"], $aud);
            jsRedirect($aud, $token);
        } else {
            showLoginForm($continue);
        }
    } else {
        showLoginForm($continue);
    }
}

function showLoginForm($continue) {
    //echo "<script>alert(window.parent.location);</script>";
    echo '<form method="get" action="http://sso.local/jwt.php">'
        . '<label>Email:<input type="text" name="email"/></label><br/>'
        . '<label>Password:<input type="password" name="password"/></label><br/>'
        . '<input type="hidden" name="continue" value="'. $continue .'"/>'
        . '<input type="submit" value="login"/>'
        . '</form>';
}
if(!isset($_COOKIE['sso_cookie'])) {
    login();
} else {
    if(isset($_GET['checkCookie']) && $_GET['checkCookie'] == 1){

        $aud = $_GET['continue'];
        $cookie = $_COOKIE['sso_cookie'];
        
        $query = Database::$pdo->prepare("SELECT * FROM users WHERE logged = 1 AND cookie = '$cookie'");
        $query->execute(array());
        $user = $query->fetch();
        if($user) {
            $token = generateJWT($user["id"], $aud);
            jsRedirect($aud, $token);
        }
    } 
    else {
    $str = <<<EOF
        <form method="get" action="http://sso.local/jwt.php">
            <input type="hidden" name="checkCookie" value="1"/>
            <input type="hidden" name="continue" value="{$_GET['continue']}"/>
            <input type="submit" value="Login with SSO" />
        </form>
EOF;
    echo $str;
    }
}

function jsRedirect($aud, $token) {
    echo "<script>window.parent.location = 'http://" . $aud . "?token=" .$token . "';</script>";
}




