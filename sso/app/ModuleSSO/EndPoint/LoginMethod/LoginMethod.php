<?php
namespace ModuleSSO\EndPoint;

use ModuleSSO\Cookie;

abstract class LoginMethod implements ILoginMethod
{   
    public $domain = CFG_JWT_ISSUER;
    public $continueUrl = '';

    /**
     * Sets and updates SSO cookie
     * @param $userId
     */
    public function setSSOCookie($userId)
    { 
        $identifier = md5(Cookie::SALT . md5(Cookie::generateHash($userId) . Cookie::SALT));
        $token = md5(uniqid(rand(), TRUE));
        $timeout = time() + 60 * 60 * 24 * 7;
        
        setcookie(Cookie::SECURE_SSO_COOKIE, "$identifier:$token", $timeout, null, null, null, true);
        
        $query = \Database::$pdo->prepare("UPDATE users SET cookie = '$identifier:$token' WHERE id = $userId");
        $query->execute();
    }


    /**
     *
     * Unsets SSO cookie
     */
    public function unsetCookies()
    {
        setcookie(Cookie::SECURE_SSO_COOKIE, null, -1, '/');
    }
    
    public function getUserFromCookie()
    {
        if(isset($_COOKIE[Cookie::SECURE_SSO_COOKIE])) {
            list($identifier, $token) = explode(":", $_COOKIE[Cookie::SECURE_SSO_COOKIE]);
            $query = \Database::$pdo->prepare("SELECT * FROM users WHERE cookie = '$identifier:$token'");
            $query->execute();
            $user = $query->fetch();
            if($user) {
                $this->setSSOCookie($user['id']);
                return $user;
            } else {
                return null;
            }
        } else {
            return null;
        }
    }
    
    public function perform()
    {
        $this->loginListener();
        $this->logoutListener();
    }
    
        
    public function getContinueUrl($continue = null)
    {
        $returnUrl = $url = CFG_SSO_ENDPOINT_URL;
        if($continue) {
            $url = $continue;
        } else if(isset($_GET[\ModuleSSO::CONTINUE_KEY])) {
            $url = $_GET[\ModuleSSO::CONTINUE_KEY];
        } else if(isset($_SERVER['HTTP_REFERER'])) {
            $url = $_SERVER['HTTP_REFERER'];
        }

        $parsed = parse_url($url);
        if(!empty($parsed['host']) && !empty($parsed['scheme'])) {
            if ($this->isInWhiteList($parsed['host'])) {
                $returnUrl = $parsed['scheme'] . '://' . $parsed['host'];
                if(!empty($parsed['path'])) {
                    $returnUrl .= $parsed['path'];
                }
            }
        }
        return $returnUrl;
    }
    
    public function isInWhiteList($domainName)
    {
        //find root domain
        $exploded = explode(".", $domainName);
        $tld = array_pop($exploded);
        $main = array_pop($exploded);
        
        $domainName = $main . "." . $tld;
        $query = \Database::$pdo->prepare("SELECT * FROM domains WHERE name = '$domainName'");
        $query->execute();
        $domain = $query->fetch();
        if($domain) {
            $this->domain = $domain['name'];
            return true;
        } else {
            return false;
        }
    }
    
    public function appendScripts()
    {
        
    }
    
    public function logoutListener()
    {
        if(isset($_GET[\ModuleSSO::LOGOUT_KEY]) && $_GET[\ModuleSSO::LOGOUT_KEY] == 1) {
            session_destroy();
            $this->unsetCookies();
            $this->redirect($this->getContinueUrl());
        }
    }
    
    public function appendStyles()
    {
        return '<link rel="stylesheet" href="css/styles.css">';
    }
    
}
