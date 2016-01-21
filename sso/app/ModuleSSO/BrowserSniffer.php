<?php
namespace ModuleSSO;

use phpbrowscap\Browscap;

class BrowserSniffer
{  
    const TMP_DIR = 'C:\wamp\tmp';
    public static $browscap = null;
    public static function init($path = TMP_DIR)
    {
        self::$browscap = new Browscap($path);
        self::$browscap->doAutoUpdate = false;
    }
    /**
     * 
     * @return string
     * @throws \Exception
     */
    public static function getVersion()
    {
        if(self::$browscap) {
            $info = self::$browscap->getBrowser(null, true);
            if(isset($info['Version'])) {
                return $info['Version'];
            } else {
                throw new \Exception("BrowserSniffer failed, Version not set");
            }
        } else {
            throw new \Exception("BrowserSniffer failed, browscap is not initialized");
        }
        
    }
    
    /**
     * Vraci nazev prohlizece v kratke forme, napr. 'chrome', 'firefox'
     * @return string
     * @throws \Exception
     */
    public static function getName()
    {
        if(self::$browscap) {
            $info = self::$browscap->getBrowser(null, true);
            if(isset($info['Browser'])) {
                return strtolower($info['Browser']);
            } else {
                throw new \Exception("BrowserSniffer failed, Browser not set");
            }
        } else {
            throw new \Exception("BrowserSniffer failed, browscap is not initialized");
        }
    }
    
    /**
     * Ladici vypis informaci o prohlizeci
     */
    public static function dump()
    {
        if(self::$browscap) {
            $info = self::$browscap->getBrowser(null, true);
            echo "<pre>";
            print_r($info);
            echo "</pre>";
        } else {
            throw new \Exception("BrowserSniffer failed, browscap is not initialized");
        }
    }
    
    /**
     * Vraci 1, pokud je zarizeni mobil nebo tablet. Jinak vraci 0.
     * @return int
     */
    public static function isMobileOrTablet()
    {
        if(self::$browscap) {
            $isMobileOrTablet = 0;
            $info = self::$browscap->getBrowser(null, true);

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
        } else {
            throw new \Exception("BrowserSniffer failed, browscap is not initialized");
        }
    }
    
    /**
     * 
     * @return string spojeny z IP adresy, jazyka prohlizece a nazvu prohlizece
     */
    public static function getFingerprint()
    {
        //get IP
        $ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
                
        //get language
        $lang = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : '';
        
        //browser info
        $name = '';
        if(self::$browscap) {
            $info = self::$browscap->getBrowser(null, true);
            $name = isset($info['browser_name']) ? $info['browser_name'] : '';
        } else {
            throw new \Exception("BrowserSniffer failed, browscap is not initialized");
        }
        
        return $ip . $lang . $name;
    }
}
BrowserSniffer::init();