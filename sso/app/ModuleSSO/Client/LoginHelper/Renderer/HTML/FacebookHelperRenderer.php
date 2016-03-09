<?php
namespace ModuleSSO\Client\LoginHelper\Renderer\HTML;

use ModuleSSO\EndPoint\LoginMethod\ThirdParty\FacebookLogin;

class FacebookHelperRenderer extends HTMLRenderer
{
    /*
    public function renderLogin($params = array())
    {
        $src = CFG_SSO_ENDPOINT_URL . '?' . \ModuleSSO::CONTINUE_KEY . '=' . CFG_DOMAIN_URL . '&' . \ModuleSSO::METHOD_KEY . '=' . FacebookLogin::METHOD_NUMBER;
        $html = '<a href="' . $src . '"><img src="' . CFG_SSO_URL . '/img/fbLogin.png"/></a>';
        echo $html;
    }
    */

}