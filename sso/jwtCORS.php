<?php
require_once 'app/config/config.inc.php';

Database::init();
function login() {
    global $whiteList;
    //Logger::log($_SERVER);
    if(isset($_SERVER['HTTP_ORIGIN']) && in_array($_SERVER['HTTP_ORIGIN'], $whiteList)){
        header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
        header('Content-Type: application/json');
        header('Access-Control-Allow-Credentials: true');
    
        if(!empty($_GET['email']) && !empty($_GET['password'])) {
            $query = Database::$pdo->prepare("SELECT * FROM users WHERE email = ? AND password = ?");
            $query->execute(array($_GET['email'], $_GET['password']));
            $user = $query->fetch();
            if($user) {
                $hash = hash('sha256', $user['id']);
                setcookie('sso_cookie', $hash);
                $query = Database::$pdo->prepare("UPDATE users SET cookie = '" . $hash . "',logged = 1 WHERE id = " . $user["id"]);
                $query->execute();
                
                $aud = $_SERVER['HTTP_ORIGIN'];
                $token = generateJWT($user["id"], $aud);
                echo '{"status": "ok", "token": "' . $token . '"}';
            } else {
                echo json_encode(array("status" => "fail"));
            }
        } else {
            //echo json_encode(array("status" => "bad_login"));
        }
    
    }
}

function checkCookie() {
    global $whiteList;
    if(isset($_SERVER['HTTP_ORIGIN']) && in_array($_SERVER['HTTP_ORIGIN'], $whiteList)){
        header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
        //header('Access-Control-Allow-Credentials: true');
        header('Content-Type: application/json');
        if(isset($_GET['checkCookie']) && $_GET['checkCookie'] == 1){
            if(!isset($_COOKIE['sso_cookie'])) {
                echo json_encode(array("status" => "no_cookie"));
            } else {
                $aud = $_GET['continue'];
                $cookie = $_COOKIE['sso_cookie'];
                $query = Database::$pdo->prepare("SELECT * FROM users WHERE logged = 1 AND cookie = '$cookie'");
                $query->execute(array());
                $user = $query->fetch();
                if($user) {
                    $token = generateJWT($user["id"], $aud);
                    echo '{"status": "ok", "token": "' . $token . '"}';
                }
            }
        }        
    }
}

login();
checkCookie();

