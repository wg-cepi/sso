<?php
namespace ModuleSSO\LoginMethod\ClassicLogin;

use ModuleSSO\LoginMethod\ClassicLogin;

class IframeLogin extends ClassicLogin
{   
    const METHOD_NUMBER = 2;
    public function showClientLogin($continue)
    {
        $data = array(
                \ModuleSSO::METHOD_KEY => self::METHOD_NUMBER,
                \ModuleSSO::CONTINUE_KEY => $continue
                );
        $query = http_build_query($data);
        $src = CFG_SSO_ENDPOINT_URL . '?' . $query;
        return "<div><iframe id='id-iframe-login' src='$src' width='100%' height='100%' scrolling='no' frameborder='0'></iframe></div>";
    }
    
    public function redirect($url)
    {
        echo "<script>window.parent.location = '" . $url . "';</script>";
    }
    
    public function showHTMLHeader()
    {

    }
    
    public function appendStyles()
    {
        return '<link rel="stylesheet" href="css/iframe.styles.css">';
    }
}