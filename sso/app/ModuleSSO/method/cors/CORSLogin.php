<?php
namespace ModuleSSO\LoginMethod;

use ModuleSSO\LoginMethod;
use ModuleSSO\JWT;
use ModuleSSO\Cookie;

class CORSLogin extends LoginMethod
{
    const METHOD_NUMBER = 3;
    
    public function checkCookie()
    {
        header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
        header('Access-Control-Allow-Credentials: true');
        header('Content-Type: application/json');
        if(!isset($_COOKIE[Cookie::SSOC])) {
            echo json_encode(array("status" => "no_cookie"));
        } else {
            $user = $this->getUserFromCookie();
            if($user) {
                $token = (new JWT($this->domain))->generate(array('uid' => $user['id']));
                echo '{"status": "ok", "' . \ModuleSSO::TOKEN_KEY . '": "' . $token . '", "email": "' . $user['email'] .'"}';
            } else {
                echo '{"status": "bad_cookie"}';
            }
        }
    }
    
    public function login()
    {
        header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
        header('Content-Type: application/json');
        header('Access-Control-Allow-Credentials: true');

        if(!empty($_GET['email']) && !empty($_GET['password'])) {
            $query = \Database::$pdo->prepare("SELECT * FROM users WHERE email = ? AND password = ?");
            $query->execute(array($_GET['email'], $_GET['password']));
            $user = $query->fetch();
            if($user) {
                $this->setCookies($user['id']);
                $token = (new JWT($this->domain))->generate(array('uid' => $user['id']));

                echo '{"status": "ok", "' . \ModuleSSO::TOKEN_KEY . '": "' . $token . '"}';
            } else {
                echo json_encode(array("status" => "user_not_found"));
            }
        } else {
            echo json_encode(array("status" => "bad_login"));
        }

        
    }
    
    public function run()
    {
        if(isset($_SERVER['HTTP_ORIGIN'])){
            $parsed = parse_url($_SERVER['HTTP_ORIGIN']);
            if(isset($parsed['host'])) {
                $query = \Database::$pdo->prepare("SELECT * FROM domains WHERE name = '" . $parsed['host'] . "'");
                $query->execute();
                $domain = $query->fetch();
                if($domain) {
                    $this->domain = $domain['name'];
                    if(isset($_GET[\ModuleSSO::LOGIN_KEY]) && $_GET[\ModuleSSO::LOGIN_KEY] == 1) {
                        $this->login();
                    } else if(isset($_GET['checkCookie']) && $_GET['checkCookie'] == 1) {
                        $this->checkCookie();
                    }
                } else {
                    //domain not allowed
                }
            } else {
                //
            }
        }
        
    }
}
