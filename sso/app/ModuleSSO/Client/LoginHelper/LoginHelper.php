<?php
namespace ModuleSSO\Client;

abstract class LoginHelper
{
    abstract public function showLogin($continue = '');
    
    public function appendScripts()
    {
        return '';
    }
    
    public function appendStyles()
    {
        return '';
    }

    public function isSupported()
    {
        return true;
    }
}

