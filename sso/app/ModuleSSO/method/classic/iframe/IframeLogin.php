<?php
namespace ModuleSSO\LoginMethod\ClassicLogin;

use ModuleSSO\LoginMethod\ClassicLogin;

class IframeLogin extends ClassicLogin
{   
    const METHOD_NUMBER = 2;
    
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