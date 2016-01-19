<?php
namespace ModuleSSO\LoginMethod;

use ModuleSSO\LoginMethod;
abstract class ThirdPartyLogin extends LoginMethod
{
    public function redirect($url = CFG_SSO_ENDPOINT_URL, $code = 302)
    {
        http_response_code($code);
        header("Location: " . $url);
        exit;
    }
}
