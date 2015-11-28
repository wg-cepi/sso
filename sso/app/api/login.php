<?php
require_once '../config/config.inc.php';
Database::init();
function apiLogin() {
    if(!empty($_GET['email']) && !empty($_GET['password'])) {
        $query = Database::$pdo->prepare("SELECT * FROM users WHERE email = ? AND password = ?");
        $query->execute(array($_GET['email'],$_GET['password']));
        $user = $query->fetch();
        if($user) {
            setcookie('sso_cookie', $user['id']);
            $token = generateJWT($user["id"], 'http://test.t/');
            
            if(isset($_GET['callback'])){
                header('Content-Type: text/javascript; charset=utf8');
                $callback = $_GET['callback'];
                echo $callback.'({"token": "'. $token.'", "state": "ok"});';
            }
            else {
                header('Content-Type: application/json');
                echo json_encode(array('state' => 'fail'));
            }
        } else {
            header('Content-Type: application/json');
            echo json_encode(array('state' => 'fail'));
        }
    } else {
        header('Content-Type: application/json');
        echo json_encode(array('state' => 'fail'));
    }
}

apiLogin();
