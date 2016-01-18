<?php
namespace ModuleSSO\LoginMethod\ClassicLogin;

use ModuleSSO\LoginMethod\ClassicLogin;

class NoScriptLogin extends ClassicLogin
{   
    const METHOD_NUMBER = 1;
    public function showClientLogin($continue)
    {
        return '
        <div id="id-login-area">
            <form id="id-sso-form" method="get" action="'. CFG_SSO_ENDPOINT_URL .'">
                <input type="hidden" name="' . \ModuleSSO::CONTINUE_KEY . '" value="' . $continue . '"/>
                <input type="hidden" name="' . \ModuleSSO::METHOD_KEY . '" value="' . self::METHOD_NUMBER . '"/>
                <input type="submit" class="button-full mdl-button mdl-js-button mdl-button--raised mdl-button--colored" id="id-login-button" value="Login with SSO"/>
            </form>
        </div>';
        
    }
    
    public function redirect($url, $code = 302)
    {
        http_response_code($code);
        header("Location: " . $url);
        exit;
    }
}