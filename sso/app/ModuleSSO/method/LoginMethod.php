<?php
namespace ModuleSSO;

use ModuleSSO\Cookie;

abstract class LoginMethod implements ILoginMethod
{   
    public $domain = CFG_JWT_ISSUER;
    public $continueUrl = '';
    
    /*
     * Sets and updates SSO cookies
     */
    public function setCookies($userId)
    { 
        $identifier = md5(Cookie::SALT . md5(Cookie::generateHash($userId) . Cookie::SALT));
        $token = md5(uniqid(rand(), TRUE));
        $timeout = time() + 60 * 60 * 24 * 7;
        
        setcookie(Cookie::SSOC, "$identifier:$token", $timeout, null, null, null, true);
        
        $query = \Database::$pdo->prepare("UPDATE users SET cookie = '$identifier:$token' WHERE id = $userId");
        $query->execute();
        
    }
    
    /*
     * Unsets SSO cookies
     */
    public function unsetCookies()
    {
        setcookie(Cookie::SSOC, null, -1, '/');
    }
    
    public function getUserFromCookie()
    {
        if(isset($_COOKIE[Cookie::SSOC])) {
            list($identifier, $token) = explode(":", $_COOKIE[Cookie::SSOC]);
            $query = \Database::$pdo->prepare("SELECT * FROM users WHERE cookie = '$identifier:$token'");
            $query->execute();
            $user = $query->fetch();
            if($user) {
                $this->setCookies($user['id']);
                return $user;
            } else {
                return null;
            }
        } else {
            return null;
        }
    }
    
    public function run()
    {
        $this->login();
        $this->logout();
    }
    
        
    public function getContinueUrl($continue = null)
    {
        if($continue) {
            $parsed = parse_url($continue);
            if(!empty($parsed['host'])) {
                if($this->isInWhitelist($parsed['host'])) {
                    return $continue;
                } else {
                    return CFG_SSO_ENDPOINT_URL;
                }
            } else {
                return CFG_SSO_ENDPOINT_URL;
            }
        }
        if(isset($_GET[\ModuleSSO::CONTINUE_KEY])) {
            $url = $_GET[\ModuleSSO::CONTINUE_KEY];
            $parsed = parse_url($url);
            if(!empty($parsed['host'])) {
                if($this->isInWhitelist($parsed['host'])) {
                    return $url;
                } else {
                    return CFG_SSO_ENDPOINT_URL;
                }
            } else {
                return CFG_SSO_ENDPOINT_URL;
            }
        } else if(isset($_SERVER['HTTP_REFERER'])) {
            $url = $_SERVER['HTTP_REFERER'];
            $parsed = parse_url($url);
            if(!empty($parsed['host'])) {
                if($this->isInWhitelist($parsed['host'])) {
                    return $url;
                } else {
                    return CFG_SSO_ENDPOINT_URL;
                }
            } else {
                return CFG_SSO_ENDPOINT_URL;
            }
        } else {
            return CFG_SSO_ENDPOINT_URL;
        }  
    }
    
    public function isInWhitelist($domainName)
    {
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
    
    public function logout()
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
