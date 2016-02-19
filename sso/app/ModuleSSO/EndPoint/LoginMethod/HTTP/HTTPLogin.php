<?php
namespace ModuleSSO\EndPoint\LoginMethod\HTTP;

use ModuleSSO\EndPoint\LoginMethod;
use ModuleSSO\Cookie;
use ModuleSSO\JWT;
use ModuleSSO\Messages;

/**
 * Class HTTPLogin
 * @package ModuleSSO\EndPoint\LoginMethod\HTTP
 */
abstract class HTTPLogin extends LoginMethod
{
    /**
     * Listens for $_GET parameters and performs appropriate commands
     *
     * If email and password are set in $_GET, method creates SSO cookie and redirects user with generated token
     * If continue key is set in $_GET, method updates SSO cookie and redirects user with generated token
     * If relog key is set in $_GET, method shows login form
     * If none of conditions mentioned above is met, method checks if SSO cookie is set and tries to obtain user, otherwise method shows login form
     *
     * @uses \ModuleSSO::LOGIN_KEY
     * @uses \ModuleSSO::RELOG_KEY
     * @uses \ModuleSSO\Cookie::SECURE_SSO_COOKIE
     */
    public function loginListener()
    {
        if(isset($_GET['email']) && isset($_GET['password'])) {
            $email =  $_GET['email'];
            $password =  $_GET['password'];

            $query = \Database::$pdo->prepare("SELECT * FROM users WHERE email = ?");
            $query->execute(array($email));
            $user = $query->fetch();
            if($user && $this->verifyPasswordHash($password, $user['password'])) {
                $this->setOrUpdateSSOCookie($user['id']);
                $this->generateTokenAndRedirect($user);
            } else {
                Messages::insert('Login failed, please try again', 'warn');
                echo $this->showHTMLLoginForm();
            }
        } else if(isset($_GET[\ModuleSSO::LOGIN_KEY])) {
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
        else {
            echo $this->showHTML();
        }
    }

    /**
     * Generates HTML login form for NoScript login and Iframe login
     *
     * @return string HTML of login form
     *
     * @uses Messages::showMessages()
     */
    public function showHTMLLoginForm()
    {
        $str = $this->showHTMLHeader();
        $str .= '<div id="id-login-area" class="mdl-card--border mdl-shadow--2dp">';
        $str .= '<span id="id-sso-login-header">Login to Webgarden SSO</span>';
        $str .= '<form id="id-sso-form" action="' . CFG_SSO_ENDPOINT_URL . '" class="' . \ModuleSSO::METHOD_KEY . static::METHOD_NUMBER . "-" . str_replace('.', '-' , $this->getDomain()) . '">'
                . '<div class="inputs">'
                        . '<div class="input-email mdl-textfield mdl-js-textfield mdl-textfield--floating-label">'
                            . '<input type="email" class="mdl-textfield__input" name="email" id="id-email"/>'
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

    /**
     * Generates header HTML
     *
     * @return string HTML of header
     */
    public function showHTMLHeader()
    {
        $str = '<h1>' . CFG_SSO_DISPLAY_NAME . '</h1>';
        return $str;
        
    }

    /**
     * Generates user information in HTML
     *
     * @param $user
     * @return string HTML containing info about user
     */
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

    /**
     * If user exists, method shows user info, otherwise shows login form
     *
     * @return string HTML string
     *
     * @uses LoginMethod::showHTMLUserInfo()
     * @uses LoginMethod::showHTMLLoginForm()
     */
    public function showHTML()
    {
        $user = $this->getUserFromCookie();
        if($user !== null) {
            return $this->showHTMLUserInfo($user);
        } else {
            return $this->showHTMLLoginForm();
        }
    }
}

