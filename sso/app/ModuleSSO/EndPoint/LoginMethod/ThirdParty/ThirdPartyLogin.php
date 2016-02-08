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
    
    public function redirectWithToken($socialId, $socialEmail) {
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
                $this->setAndUpdateSSOCookie($user['id']);

                $redirectUrl = $this->getContinueUrl();
                if($redirectUrl !== CFG_SSO_ENDPOINT_URL) {
                    $token = (new JWT($this->getDomain()))->generate(array('uid' => $user['id']));

                    $query = \ModuleSSO::TOKEN_KEY . '=' . $token;
                    $redirectUrl = $redirectUrl .  "?" . $query;
                }
                $this->redirect($redirectUrl);
            } else {
                $data = array(
                    \ModuleSSO::METHOD_KEY => static::METHOD_NUMBER,
                    \ModuleSSO::CONTINUE_KEY => $this->getContinueUrl()
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

            $this->setAndUpdateSSOCookie($user['id']);

            $redirectUrl = $this->getContinueUrl();
            if($redirectUrl !== CFG_SSO_ENDPOINT_URL) {
                $token = (new JWT($this->getDomain()))->generate(array('uid' => $user['id']));
                $redirectUrl = $redirectUrl .  "?" . \ModuleSSO::TOKEN_KEY . "=" . $token;
            }
             $this->redirect($redirectUrl);
        }
    }
}
