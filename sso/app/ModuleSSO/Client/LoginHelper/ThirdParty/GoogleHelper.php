<?php
namespace ModuleSSO\Client\LoginHelper\ThirdParty;

use ModuleSSO\EndPoint\LoginMethod\ThirdParty\GoogleLogin;

class GoogleHelper extends ThirdPartyHelper
{
    public function showLogin($continue = '')
    {
        $src = CFG_SSO_ENDPOINT_URL . '?' . \ModuleSSO::CONTINUE_KEY . '=' . CFG_DOMAIN_URL . '&' . \ModuleSSO::METHOD_KEY . '=' . GoogleLogin::METHOD_NUMBER;
        return '<a href="' . $src . '"><img src="' . CFG_SSO_URL . '/img/googleLogin.png"/></a>';
    }
}
