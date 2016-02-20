<?php

/**
 * Class ModuleSSO
 *
 * @abstract
 */
abstract class ModuleSSO
{
    /**
     * @var string
     */
    const TOKEN_KEY = 'sso_token';

    /**
     * @var string
     */
    const RELOG_KEY = 'relog';

    /**
     * @var string
     */
    const LOGIN_KEY = 'login';

    /**
     * @var string
     */
    const CHECK_COOKIE_KEY = 'check_cookie';

    /**
     * @var string
     */
    const METHOD_KEY = 'm';

    /**
     * @var string
     */
    const CONTINUE_KEY = 'continue';

    /**
     * @var string
     */
    const FORCED_METHOD_KEY = 'f';

    /**
     * @var string
     */
    const LOGOUT_KEY = 'logout';

    /**
     * @var string
     */
    const GLOBAL_LOGOUT_KEY = 'glogout';

    /**
     * @var string
     */
    const MESSAGES_KEY = 'messages';
    
    /**
     * @return mixed
     * @abstract
     */
    abstract public function run();

    /**
     * Hashes password by user
     *
     * @param string $password Plain password
     * @return string Hashed password
     */
    public static function generatePasswordHash($password)
    {
        //automatic salt
        return crypt($password);
    }

    /**
     * Compares password gived by user and stored hashed password
     *
     * @link http://us.php.net/manual/en/function.hash-equals.php#118384
     *
     * @param string $password Password provided by user
     * @param string $hashedPassword Hashed password
     *
     * @return bool
     */
    public static function verifyPasswordHash($password, $hashedPassword)
    {
        return substr_count($hashedPassword ^ crypt($password, $hashedPassword), "\0") * 2 === strlen($hashedPassword . crypt($password, $hashedPassword));
    }
}