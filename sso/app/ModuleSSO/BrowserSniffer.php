<?php
namespace ModuleSSO;

use phpbrowscap\Browscap;

class BrowserSniffer
{
    /**
     * @var string TMP_DIR Directory for Browscap cache
     */
    const TMP_DIR = 'C:\wamp\tmp';

    /**
     * @var Browscap $browscap
     */
    public static $browscap = null;

    /**
     * Initializes whole sniffer
     * @param string $path Optional parameter of directory with cached browser information
     */
    public static function init($path = self::TMP_DIR)
    {
        self::$browscap = new Browscap($path);

        //very time and memory consuming process, does not have to be done every time
        self::$browscap->doAutoUpdate = false;
    }

    /**
     * Returns version of the browser
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
     * Returns name of the browser in short form, eg. 'chrome', 'firefox'
     *
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
     * Debug output
     *
     * @return string
     * @throws \Exception
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
     * Method returns 1 if browsing device is mobile or tablet, otherwise returns 0
     *
     * @return int
     * @throws \Exception
     */
    public static function isMobileOrTablet()
    {
        if(self::$browscap) {
            $isMobileOrTablet = 0;
            $info = self::$browscap->getBrowser(null, true);

            if(!isset($info['isTablet']) && !isset($info['isMobileDevice'])) {
                throw new \Exception("BrowserSniffer failed, isTablet and isMobileDevice not set");
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
     * Returns pseudo-fingerprint of browser
     *
     * @return string concatenation of IP address, browser language and browser name
     * @throws \Exception
     */
    public static function getFingerprint()
    {
        //get IP
        $ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
                
        //get language
        $lang = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : '';
        
        //browser info
        if(self::$browscap) {
            $info = self::$browscap->getBrowser(null, true);
            $name = isset($info['browser_name']) ? $info['browser_name'] : '';
        } else {
            throw new \Exception("BrowserSniffer failed, browscap is not initialized");
        }
        
        return $ip . $lang . $name;
    }
}