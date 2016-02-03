<?php
namespace ModuleSSO\EndPoint\LoginMethod\HTTP;

class NoScriptLogin extends HTTPLogin
{   
    const METHOD_NUMBER = 1;
    
    public function redirect($url, $code = 302)
    {
        http_response_code($code);
        header("Location: " . $url);
        exit;
    }
    
}