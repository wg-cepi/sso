<?php
namespace ModuleSSO\ClientLoginMethod;

use ModuleSSO\ClientLoginMethod\ClientThirdPartyLogin;
use ModuleSSO\LoginMethod\ThirdPartyLogin\GoogleLogin;

class ClientGoogleLogin extends ClientThirdPartyLogin
{
    public function showLogin($continue = '')
    {
        $src = CFG_SSO_ENDPOINT_URL . '?' . \ModuleSSO::CONTINUE_KEY . '=' . CFG_DOMAIN_URL . '&' . \ModuleSSO::METHOD_KEY . '=' . GoogleLogin::METHOD_NUMBER;
        return '<a href="' . $src . '"><img src="' . CFG_SSO_URL . '/img/googleLogin.png"/></a>';
    }
}
