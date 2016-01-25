<?php

abstract class ModuleSSO
{
    const TOKEN_KEY = 'sso_token';
    const RELOG_KEY = 'relog';
    const LOGIN_KEY = 'login';
    const METHOD_KEY = 'm';
    const CONTINUE_KEY = 'continue';
    const FORCED_METHOD_KEY = 'f';
    const LOGOUT_KEY = 'logout';
    const GLOBAL_LOGOUT_KEY = 'glogout';
    const MESSAGES_KEY = 'messages';
    
    abstract public function run();
    abstract public function pickLoginMethod();
}