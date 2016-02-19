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
}