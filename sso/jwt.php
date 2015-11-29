<?php
require_once 'app/config/config.inc.php';

Database::init();

function login() {
    $params = array(
        'continue' => isset($_POST['continue']) ? $_POST['continue'] : (isset($_GET['continue']) ? $_GET['continue'] : "")
    );
    //print_r($_POST);
    if(!empty($_POST['email']) && !empty($_POST['password'])) {
        $query = Database::$pdo->prepare("SELECT * FROM users WHERE email = ? AND password = ?");
        $query->execute(array($_POST['email'],$_POST['password']));
        $user = $query->fetch();
        if($user) {
            $hash = hash('sha256', $user['id']);
            setcookie('sso_cookie', $hash);
            $query = Database::$pdo->prepare("UPDATE users SET cookie = '" . $hash . "',logged = 1 WHERE id = " . $user["id"]);
            $query->execute();
            $aud = $params['continue'];
            $token = generateJWT($user["id"], $aud);
            jsRedirect($aud, $token);
        } else {
            showLoginForm($params);
        }
    } else {
        showLoginForm($params);
    }
}

function showLoginForm(Array $params) {
    //echo "<script>alert(window.parent.location);</script>";
    echo '<form method="post" action="http://sso.local/jwt.php">'
        . '<label>Email:<input type="text" name="email"/></label><br/>'
        . '<label>Password:<input type="password" name="password"/></label><br/>'
        . '<input type="hidden" name="continue" value="'.$params['continue'].'"/>'
        . '<input type="submit" value="login"/>'
        . '</form>';
}
if(!isset($_COOKIE['sso_cookie'])) {
    login();
} else {
    if(isset($_REQUEST['checkCookie']) && $_REQUEST['checkCookie'] == 1){

        $aud = $_REQUEST['continue'];
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
        <form method="post" action="http://sso.local/jwt.php">
            <input type="hidden" name="checkCookie" value="1"/>
            <input type="hidden" name="continue" value="{$_GET['continue']}"/>
            <input type="submit" value="Login with SSO" />
        </form>
EOF;
    echo $str;
    }
}



function jsRedirect($aud, $token) {
    echo "<script>window.parent.location = '" . $aud . "?token=" .$token . "';console.log(window.parent.location);</script>";
}


function getUser() {
    $query = Database::$pdo->prepare("SELECT * FROM users WHERE id = ?");
    $query->execute(array($_COOKIE['sso_cookie']));
    return $query->fetch();
}




