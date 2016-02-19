<?php
namespace ModuleSSO;

/**
 * Class Cookie
 * @package ModuleSSO
 */
class Cookie
{
    /**
     * @var string Name of SSO cookie
     */
    const SECURE_SSO_COOKIE = 'SSSOC';

    /**
     * @var string Value used for fingerprint generation
     */
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