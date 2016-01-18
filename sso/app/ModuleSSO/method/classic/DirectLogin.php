<?php
namespace ModuleSSO\LoginMethod\ClassicLogin;

use ModuleSSO\LoginMethod\ClassicLogin;

class DirectLogin extends ClassicLogin
{
    public function redirect($url = CFG_SSO_ENDPOINT_URL, $code = 302)
    {
        http_response_code($code);
        header("Location: " . $url);
        exit;

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
                . '<div class="button-wrap">'
                    . '<input type="submit" class="button-full mdl-button mdl-js-button mdl-button--raised mdl-button--colored" id="id-login-button" value="Login with SSO"/>'
                .'</div>'
            . '</form>';
        $str .= '</div>';
        return $str;
    }
   
    public function showHTMLUserInfo($user)
    {
        $html = $this->showHTMLHeader();
        $html .= '<div id="id-sso-link"><p>You are logged in as <strong>' . $user['email'] . '</strong> at Webgarden SSO</p><ul>';
        $html .= '<li><a href="' . CFG_SSO_ENDPOINT_URL . '?' . \ModuleSSO::RELOG_KEY . '=1" title="Log in as another user">Log in as another user to Webgarden SSO</a>';
        $html .= '<li><a href="?' . \ModuleSSO::LOGOUT_KEY. '=1" title="Logout">Logout from Webgarden SSO</a>';
        $html .= '</ul></div>';
        return $html;
    }
}

