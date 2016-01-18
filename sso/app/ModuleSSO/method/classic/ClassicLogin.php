<?php
namespace ModuleSSO\LoginMethod;

use ModuleSSO\LoginMethod;
use ModuleSSO\Cookie;
use ModuleSSO\JWT;

abstract class ClassicLogin extends LoginMethod
{
    public function login()
    {
        $this->continueUrl = $this->getContinueUrl();
        if(isset($_GET['email']) && isset($_GET['password'])) {
            $email =  $_GET['email'];
            $password =  $_GET['password'];

            $query = \Database::$pdo->prepare("SELECT * FROM users WHERE email = ? AND password = ?");
            $query->execute(array($email, $password));
            $user = $query->fetch();
            if($user) {
                $this->setCookies($user['id']);
                $token = (new JWT($this->domain))->generate(array('uid' => $user['id']));

                $url = $this->continueUrl .  "?" . \ModuleSSO::TOKEN_KEY . "=" . $token;
                $this->redirect($url);
            } else {
                echo $this->showHTMLLoginForm();
            }
        } else if(isset($_GET['login'])) {
            if(isset($_COOKIE[Cookie::SSOC])) {
                $user = $this->getUserFromCookie();
                if($user) {
                    $token = (new JWT($this->domain))->generate(array('uid' => $user['id']));
                    $url = $this->continueUrl .  "?" . \ModuleSSO::TOKEN_KEY . "=" . $token;
                    $this->redirect($url);
                } else {
                    echo $this->showHTMLLoginForm();
                }
            } else {
                echo $this->showHTMLLoginForm();
            }
        }
        else if (isset($_GET[\ModuleSSO::RELOG_KEY])){
            echo $this->showHTMLLoginForm();
        }
        else if(isset($_GET[\ModuleSSO::LOGOUT_KEY])) {
            echo $this->showHTMLLoginForm();
        } else {
            $this->showHTML();
        }
    }
    
    public function showHTMLLoginForm()
    {
        $str = $this->showHTMLHeader();
        $str .= '<div id="id-login-area" class="mdl-card--border mdl-shadow--2dp">';
        $str .= '<form id="id-sso-form" action="' . CFG_SSO_ENDPOINT_URL . '">'
                . '<div class="inputs">'
                        . '<div class="input-email">'
                            . '<label for="id-email">'
                                . 'Email'
                            . '</label>'
                            . '<input type="text" class="block" name="email" id="id-email"/>'
                        . '</div>'
                        . '<div class="input-pass">'
                            . '<label for="id-pass">'
                                . 'Password'
                            . '</label>'
                            . '<input type="password" class="block" name="password" id="id-pass"/>'
                        . '</div>'
                . '</div>'
                . ' <input type="hidden" name="' . \ModuleSSO::CONTINUE_KEY . '" value="' . $this->continueUrl .  '"/>'
                . '<input type="hidden" name="' . \ModuleSSO::METHOD_KEY . '" value="' . static::METHOD_NUMBER . '"/>'
                . '<div class="button-wrap">'
                    . '<input type="submit" class="button-full mdl-button mdl-js-button mdl-button--raised mdl-button--colored" id="id-login-button" value="Login with SSO"/>'
                .'</div>'
            . '</form>';
        $str .= '</div>';
        return $str;
        
    }
    
    public function showHTMLHeader()
    {
        $str = '<h1>Webgarden SSO</h1>';
        return $str;
        
    }
    
    public function showHTMLUserInfo($user)
    {
        $html = $this->showHTMLHeader();
        $html .= '<div id="id-sso-links"><p>You are logged in as <strong>' . $user['email'] . '</strong> at <a href="' . CFG_SSO_ENDPOINT_URL . '">Webgarden SSO</a></p><ul>';
        if ($this->continueUrl !== CFG_SSO_ENDPOINT_URL) {
            $data = array(
                \ModuleSSO::METHOD_KEY => static::METHOD_NUMBER,
                \ModuleSSO::LOGIN_KEY => 1,
                \ModuleSSO::CONTINUE_KEY => $this->continueUrl
                );
            $query = http_build_query($data);
            $src = CFG_SSO_ENDPOINT_URL . '?' . $query;
            $html .= '<li><a href="' . $src . '" title="Continue as ' . $user['email'] . '"> Continue as ' . $user['email'] . '</a></li>';
        }
        $data = array(
                \ModuleSSO::METHOD_KEY => static::METHOD_NUMBER,
                \ModuleSSO::RELOG_KEY => 1,
                \ModuleSSO::CONTINUE_KEY => $this->continueUrl
                );
        $query = http_build_query($data);
        $src = CFG_SSO_ENDPOINT_URL . '?' . $query;
        $html .= '<li><a href="' . $src . '" title="Log in as another user">Log in as another user</a></ul></div>';
        return $html; 
    }
    
    public function showHTML()
    {
        $user = $this->getUserFromCookie();
        if($user !== null) {
            echo $this->showHTMLUserInfo($user);
        } else {
            echo $this->showHTMLLoginForm();
        }
    } 
}

