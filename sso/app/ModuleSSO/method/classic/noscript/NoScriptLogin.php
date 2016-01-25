<?php
namespace ModuleSSO\LoginMethod\ClassicLogin;

use ModuleSSO\LoginMethod\ClassicLogin;

class NoScriptLogin extends ClassicLogin
{   
    const METHOD_NUMBER = 1;
    
    public function redirect($url, $code = 302)
    {
        http_response_code($code);
        header("Location: " . $url);
        exit;
    }
    
}