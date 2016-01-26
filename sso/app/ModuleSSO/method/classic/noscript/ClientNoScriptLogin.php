<?php
namespace ModuleSSO\ClientLoginMethod\ClientClassicLogin;

use ModuleSSO\ClientLoginMethod\ClientClassicLogin;
use \ModuleSSO\LoginMethod\ClassicLogin\NoScriptLogin;

class ClientNoScriptLogin extends ClientClassicLogin
{
    public function showLogin($continue = '')
    {
        return '
        <div id="id-login-area">
            <form id="id-sso-form" method="get" action="'. CFG_SSO_ENDPOINT_URL .'">
                <input type="hidden" name="' . \ModuleSSO::CONTINUE_KEY . '" value="' . $continue . '"/>
                <input type="hidden" name="' . \ModuleSSO::METHOD_KEY . '" value="' . NoScriptLogin::METHOD_NUMBER . '"/>
                <input type="submit" class="button-full mdl-button mdl-js-button mdl-button--raised mdl-button--colored" id="id-login-button" value="Login with SSO"/>
            </form>
        </div>';
        
    }
}