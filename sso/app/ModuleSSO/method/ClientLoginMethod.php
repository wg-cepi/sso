<?php
namespace ModuleSSO;

abstract class ClientLoginMethod
{
    abstract public function showLogin($continue = '');
    
    public function appendScripts()
    {
        
    }
    
    public function appendStyles()
    {
        
    }
}

