<?php
namespace ModuleSSO\EndPoint\LoginMethod\HTTP;


class IframeLogin extends HTTPLogin
{   
    const METHOD_NUMBER = 2;
    
    public function redirect($url = CFG_SSO_ENDPOINT_URL, $code = 302)
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