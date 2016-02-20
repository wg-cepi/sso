<?php
namespace ModuleSSO\EndPoint\LoginMethod\HTTP;

use ModuleSSO\Messages;

/**
 * Class DirectLogin
 * @package ModuleSSO\EndPoint\LoginMethod\HTTP
 */
class DirectLogin extends HTTPLogin
{
    /**
     * @var int Number of login method
     */
    const METHOD_NUMBER = 0;

    /**
     * {@inheritdoc}
     */
    public function showHTMLLoginForm()
    {
        $str = $this->showHTMLHeader();
        $str .= '<div id="id-login-area" class="mdl-card--border mdl-shadow--2dp">';
        $str .= '<form id="id-sso-form">'
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
                . '<div class="button-wrap">'
                    . '<input type="submit" class="button-full mdl-button mdl-js-button mdl-button--raised" id="id-login-button" value="Login with SSO"/>'
                .'</div>'
                . Messages::showMessages()
            . '</form>';
        $str .= '</div>';
        return $str;
    }

    /**
     * {@inheritdoc}
     */
    public function showHTMLContinueOrRelog($user)
    {
        $html = $this->showHTMLHeader();
        $html .= '<div id="id-sso-link"><p>You are logged in as <strong>' . $user['email'] . '</strong> at Webgarden SSO</p><ul>';
        $html .= '<li><a href="' . CFG_SSO_ENDPOINT_URL . '?' . \ModuleSSO::RELOG_KEY . '=1" title="Log in as another user">Log in as another user to Webgarden SSO</a>';
        $html .= '<li><a href="?' . \ModuleSSO::LOGOUT_KEY. '=1" title="Logout">Logout from Webgarden SSO</a>';
        $html .= '</ul></div>';
        return $html;
    }

    /**
     * {@inheritdoc}
     */
    protected function generateTokenAndRedirect($user)
    {
        $this->redirect(CFG_SSO_ENDPOINT_INDEX_URL);
    }
 
}

