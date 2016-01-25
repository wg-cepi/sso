<?php
namespace ModuleSSO\ClientLoginMethod;

use \ModuleSSO\ClientLoginMethod;

abstract class ClientClassicLogin extends ClientLoginMethod
{
    public function appendStyles()
    {
        return '<link rel="stylesheet" href="' . CFG_SSO_URL . '/css/styles.css">';
    }
    
}