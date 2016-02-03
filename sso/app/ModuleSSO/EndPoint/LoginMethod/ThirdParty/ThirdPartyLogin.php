<?php
namespace ModuleSSO\EndPoint\LoginMethod\ThirdParty;

use ModuleSSO\EndPoint\LoginMethod;
use ModuleSSO\JWT;

abstract class ThirdPartyLogin extends LoginMethod
{   
    public function redirect($url = CFG_SSO_ENDPOINT_URL, $code = 302)
    {
        http_response_code($code);
        header("Location: " . $url);
        exit;
    }
    
    public function redirectWithToken($socialId, $socialEmail, $continueUrl) {
         //try to find user in facebook login pair table
        $query = \Database::$pdo->prepare("SELECT * FROM " . static::TABLE . " WHERE " . static::TABLE_COLUMN . " = '$socialId'");
        $query->execute();
        $socialUser = $query->fetch();
        \Logger::log(print_pre($socialUser['user_id'], true));
                
        if($socialUser) {
            //find real user
            $query = \Database::$pdo->prepare("SELECT * FROM users WHERE id = " . $socialUser['user_id']);
            $query->execute();
            $user = $query->fetch();
            if($user) {
                $this->setCookies($user['id']);
                
                if($continueUrl !== CFG_SSO_ENDPOINT_URL) {
                    $token = (new JWT($this->domain))->generate(array('uid' => $user['id']));

                    $query = \ModuleSSO::TOKEN_KEY . '=' . $token; 
                    $continueUrl = $continueUrl .  "?" . $query;
                }
                $this->redirect($continueUrl);
            } else {
                $data = array(
                    \ModuleSSO::METHOD_KEY => self::METHOD_NUMBER,
                    \ModuleSSO::CONTINUE_KEY => $continueUrl
                    );
                $query = http_build_query($data);
                $this->redirect(CFG_SSO_ENDPOINT_URL . '?' .  $query);
            }
        } else {
            //no user found, let's create one
            $query = \Database::$pdo->prepare("INSERT INTO users (email) VALUES ('$socialEmail')");
            $query->execute();

            $query = \Database::$pdo->prepare("SELECT * FROM users WHERE email = '$socialEmail'");
            $query->execute();
            $user = $query->fetch();

            $query = \Database::$pdo->prepare("INSERT INTO " . static::TABLE . " (user_id, " . static::TABLE_COLUMN . ", created) VALUES (" . $user['id'] . ", '$socialId', " . time() . ")");
            $query->execute();

            $this->setCookies($user['id']);
            
            if($continueUrl !== CFG_SSO_ENDPOINT_URL) {
                $token = (new JWT($this->domain))->generate(array('uid' => $user['id']));
                $continueUrl = $continueUrl .  "?" . \ModuleSSO::TOKEN_KEY . "=" . $token;
            }
             $this->redirect($continueUrl);
        }
    }
}