<?php
namespace ModuleSSO\Client\LoginHelper\HTTP;

use ModuleSSO\EndPoint\LoginMethod\HTTP\IframeLogin;

class IframeHelper extends HTTPHelper
{
    public function showLogin($continue = '')
    {
        $data = array(
                \ModuleSSO::METHOD_KEY => IframeLogin::METHOD_NUMBER,
                \ModuleSSO::CONTINUE_KEY => $continue
                );
        $query = http_build_query($data);
        $src = CFG_SSO_ENDPOINT_URL . '?' . $query;
        return "<div><iframe id='id-iframe-login' src='$src' width='100%' height='100%' scrolling='no' frameborder='0'></iframe></div>";
    }
    
    public function appendStyles()
    {
        return '<link rel="stylesheet" href="' . CFG_SSO_URL . '/css/iframe.styles.css">';
    }
}