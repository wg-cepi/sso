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
            setcookie('sso_cookie', $user['id']); //plus uložit v databázi 
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
    echo '<form method="post" action="http://sso.localhost/jwt.php">'
        . '<label>Email:<input type="text" name="email"/></label><br/>'
        . '<label>Password:<input type="password" name="password"/></label><br/>'
        . '<input type="hidden" name="continue" value="'.$params['continue'].'"/>'
        . '<input type="submit" value="login"/>'
        . '</form>';
}
if(!isset($_COOKIE['sso_cookie'])) {
    login();
} else {
    if(isset($_POST['login']) && $_POST['login'] == 1){
        $params = array(
            'continue' => $_POST['continue']
        );
        $query = Database::$pdo->prepare("SELECT * FROM users WHERE id = ?");
        $query->execute(array($_COOKIE['sso_cookie']));
        $user = $query->fetch();
        if($user) {
            //setcookie('sso_cookie', $user['id']);
            $aud = $params['continue'];
            $token = generateJWT($user["id"], $aud);
            jsRedirect($aud, $token);
        }
        exit;
    }
    $str = <<<EOF
    <form method="post" action="http://sso.localhost/jwt.php">
        <input type="hidden" name="login" value="1"/>
        <input type="hidden" name="continue" value="{$_GET['continue']}"/>
        <input type="submit" value="Login with SSO" />
    </form>
EOF;
    echo $str;
}



function jsRedirect($aud, $token) {
    echo "<script>window.parent.location = '" . $aud . "?token=" .$token . "';console.log(window.parent.location);</script>";
}


function getUser() {
    $query = Database::$pdo->prepare("SELECT * FROM users WHERE id = ?");
    $query->execute(array($_COOKIE['sso_cookie']));
    return $query->fetch();
}




