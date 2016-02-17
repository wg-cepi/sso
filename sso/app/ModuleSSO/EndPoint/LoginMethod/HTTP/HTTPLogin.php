<?php
namespace ModuleSSO\EndPoint\LoginMethod\HTTP;

use ModuleSSO\EndPoint\LoginMethod;
use ModuleSSO\Cookie;
use ModuleSSO\JWT;
use ModuleSSO\Messages;

abstract class HTTPLogin extends LoginMethod
{
    public function loginListener()
    {
        if(isset($_GET['email']) && isset($_GET['password'])) {
            $email =  $_GET['email'];
            $password =  $_GET['password'];

            $query = \Database::$pdo->prepare("SELECT * FROM users WHERE email = ? AND password = ?");
            $query->execute(array($email, $password));
            $user = $query->fetch();
            if($user) {
                $this->setAndUpdateSSOCookie($user['id']);
                $this->generateTokenAndRedirect($user);
            } else {
                Messages::insert('Login failed, please try again', 'warn');
                echo $this->showHTMLLoginForm();
            }
        } else if(isset($_GET['login'])) {
            if(isset($_COOKIE[Cookie::SECURE_SSO_COOKIE])) {
                $user = $this->getUserFromCookie();
                if($user) {
                    $this->generateTokenAndRedirect($user);
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
        $str .= '<span id="id-sso-login-header">Login to Webgarden SSO</span>';
        $str .= '<form id="id-sso-form" action="' . CFG_SSO_ENDPOINT_URL . '">'
                . '<div class="inputs">'
                        . '<div class="input-email mdl-textfield mdl-js-textfield mdl-textfield--floating-label">'
                            . '<input type="text" class="mdl-textfield__input" name="email" id="id-email"/>'
                            . '<label for="id-email" class="mdl-textfield__label">'
                                . 'Email'
                            . '</label>'
                            
                        . '</div>'
                        . '<div class="input-pass mdl-textfield mdl-js-textfield mdl-textfield--floating-label">'
                            . '<label for="id-pass" class="mdl-textfield__label">'
                                . 'Password'
                            . '</label>'
                            . '<input type="password" class="mdl-textfield__input" name="password" id="id-pass"/>'
                        . '</div>'
                . '</div>'
                . ' <input type="hidden" name="' . \ModuleSSO::CONTINUE_KEY . '" value="' . $this->continueUrl .  '"/>'
                . '<input type="hidden" name="' . \ModuleSSO::METHOD_KEY . '" value="' . static::METHOD_NUMBER . '"/>'
                . '<div class="button-wrap">'
                    . '<input type="submit" class="button-full mdl-button mdl-js-button mdl-button--raised" id="id-login-button" value="Login with SSO"/>'
                .'</div>'
                . Messages::showMessages()
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
    
    public function generateTokenAndRedirect($user)
    {
        $url = $this->continueUrl;
        if($this->continueUrl !== CFG_SSO_ENDPOINT_URL) {
            $token = (new JWT($this->getDomain()))->generate(array('uid' => $user['id']));
            $url .=  "?" . \ModuleSSO::TOKEN_KEY . "=" . $token;
        }
        $this->redirect($url);
        
    }
}

