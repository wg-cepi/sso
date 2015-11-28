<?php
require_once 'app/config/config.inc.php';

Database::init();
function login() {
    global $whiteList;
    if(isset($_SERVER['HTTP_ORIGIN']) && in_array($_SERVER['HTTP_ORIGIN'], $whiteList)){
        header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
        header('Content-Type: application/json');
    
        if(!empty($_GET['email']) && !empty($_GET['password'])) {
            $query = Database::$pdo->prepare("SELECT * FROM users WHERE email = ? AND password = ?");
            $query->execute(array($_GET['email'], $_GET['password']));
            $user = $query->fetch();
            if($user) {
                setcookie('sso_cookie', $user['id']); //plus uložit v databázi 
                $aud = $_SERVER['HTTP_ORIGIN'];
                $token = generateJWT($user["id"], $aud);
                echo '{"status": "ok", "token": "' . $token . '"}';
            } else {
                echo json_encode(array("status" => "fail", "message" => "User does not exist"));
            }
        } else {
            echo json_encode(array("status" => "bad_login", "message" => "Bad credentials."));
        }
    
    }
}

login();

