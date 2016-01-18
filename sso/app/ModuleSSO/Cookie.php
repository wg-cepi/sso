<?php
namespace ModuleSSO;

class Cookie
{
    /*
     * @var SSOC Bezpecnostni SSO cookie nutna k rozpoznani uzivatele
     * Vypocita se jako sha1(browser fingerprint) . sha1(user_id)
     */
    const SSOC = 'SSSOC';
    const SALT = 'PEPPER';
    
    public static function generateHash($userId)
    {
        return base64_encode(sha1(BrowserSniffer::getFingerprint()) . sha1($userId)); 
    }
}