<?php
namespace ModuleSSO\Client\LoginHelper\Other;

use ModuleSSO\Client\LoginHelper;
use ModuleSSO\BrowserSniffer;

/**
 * Class CORSHelper
 * @package ModuleSSO\Client\LoginHelper\Other
 */
class CORSHelper extends LoginHelper
{
    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function appendScripts()
    {
        $script = '<script src="' . CFG_SSO_URL . '/js/prototype.js"></script>';
        $script .= '<script src="' . CFG_SSO_URL . '/js/cors.js"></script>';
        return $script;
        
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function appendStyles()
    {
        return '<link rel="stylesheet" href="' . CFG_SSO_URL . '/css/styles.css">';
    }


    /**
     * {@inheritdoc}
     *
     * @return bool
     * @throws \Exception
     *
     * @uses ModuleSSO\BrowserSniffer
     */
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
