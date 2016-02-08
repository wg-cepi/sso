<?php
namespace ModuleSSO;

class Cookie
{
    const SECURE_SSO_COOKIE = 'SSSOC';
    const SALT = 'PEPPER';


    /**
     * Generates base64-encoded hash from input parameter and pseudo browser fingerprint
     *
     * @uses BrowserSniffer::getFingerprint()
     *
     * @param $userId
     * @return string
     * @throws \Exception
     */
    public static function generateHash($userId)
    {
        return base64_encode(sha1(BrowserSniffer::getFingerprint()) . sha1($userId)); 
    }
}