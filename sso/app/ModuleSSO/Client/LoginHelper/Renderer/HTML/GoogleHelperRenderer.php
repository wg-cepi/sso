<?php
namespace ModuleSSO\Client\LoginHelper\Renderer\HTML;

use ModuleSSO\EndPoint\LoginMethod\ThirdParty\GoogleLogin;

class GoogleHelperRenderer extends HTMLRenderer
{
    /*
    public function renderLogin($params = array())
    {
        $src = CFG_SSO_ENDPOINT_URL . '?' . \ModuleSSO::CONTINUE_KEY . '=' . CFG_DOMAIN_URL . '&' . \ModuleSSO::METHOD_KEY . '=' . GoogleLogin::METHOD_NUMBER;
        $html =  '<a href="' . $src . '"><img src="' . CFG_SSO_URL . '/img/googleLogin.png"/></a>';
        echo $html;
    }
    */

}