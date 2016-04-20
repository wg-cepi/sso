<?php
namespace ModuleSSO\Client\LoginHelper\Other;

use ModuleSSO\Client\LoginHelper;
use ModuleSSO\BrowserSniffer;
use ModuleSSO\Messages;

class CORSHelper extends LoginHelper
{
    public function showLogin($continue = '')
    {
       $this->renderer->renderLogin();
    }
    
    public function appendScripts()
    {
        $script = '<script src="' . CFG_SSO_URL . '/js/prototype.js"></script>';
        $script .= '<script src="' . CFG_SSO_URL . '/js/cors.js"></script>';
        return $script;
        
    }
    
    public function appendStyles()
    {
        return '<link rel="stylesheet" href="' . CFG_SSO_URL . '/css/styles.css">';
    }
    
    public function isSupported()
    {
        global $CORSBrowsers;
        if(isset($CORSBrowsers[BrowserSniffer::getName()])) {
            if(BrowserSniffer::getVersion() >= $CORSBrowsers[BrowserSniffer::getName()]) {
                return true;
            }
        } else {
            return false;
        }
    }
    
}
