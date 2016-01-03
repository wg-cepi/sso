<?php
require_once '../../vendor/autoload.php';
use phpbrowscap\Browscap;

class BrowserSniffer
{  
    /**
     * 
     * @return string
     * @throws \Exception
     */
    public static function getVersion()
    {
        $browscap = new Browscap('C:\wamp\tmp');
        $info = $browscap->getBrowser(null, true);
        if(isset($info['Version'])) {
            return $info['Version'];
        } else {
            throw new \Exception("BrowserSniffer failed, Version not set");
        }
        
    }
    
    /**
     * 
     * @return string
     * @throws \Exception
     */
    public static function getName()
    {
        $browscap = new Browscap('C:\wamp\tmp');
        $info = $browscap->getBrowser(null, true);
        if(isset($info['Browser'])) {
            return strtolower($info['Browser']);
        } else {
            throw new \Exception("BrowserSniffer failed, Browser not set");
        }
        //TODO add more checks
        
    }
    
    /**
     * 
     * Dumps browser data
     */
    public static function dump()
    {
        $browscap = new Browscap('C:\wamp\tmp');
        $info = $browscap->getBrowser(null, true);
        echo "<pre>";
        print_r($info);
        echo "</pre>";
    }
    
    /**
     * Returns 1 if device is mobile or tablet, otherwise returns 0
     * @return int
     */
    public static function isMobileOrTablet()
    {
        $isMobileOrTablet = 0;
        $browscap = new Browscap('C:\wamp\tmp');
        $info = $browscap->getBrowser(null, true);
        
        if(!isset($info['isTablet']) && !isset($info['isMobileDevice'])) {
            throw new Exception("BrowserSniffer failed, isTablet and isMobileDevice not set");
        }
        
        if(isset($info['isTablet'])) {
            $isMobileOrTablet |= $info['isTablet'];
        }
        if(isset($info['isMobileDevice']))
        {
            $isMobileOrTablet |= $info['isMobileDevice'];
        }
        return $isMobileOrTablet;
    }
}
/*
BrowserSniffer::dump();
echo "<br>";
echo BrowserSniffer::getName();
echo "<br>";
echo BrowserSniffer::getVersion();
echo "<br>";
echo BrowserSniffer::isMobileOrTablet();
echo "<br>";
*/