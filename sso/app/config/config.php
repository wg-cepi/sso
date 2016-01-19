<?php
define('CFG_SQL_HOST', 'localhost');
define('CFG_SQL_DBNAME', 'sso');
define('CFG_SQL_USERNAME', 'root');
define('CFG_SQL_PASSWORD', '');
define('CFG_JWT_ISSUER', 'sso.local');
define('CFG_SSO_ENDPOINT_URL', 'http://sso.local/login.php');
define('CFG_SSO_ENDPOINT_PLAIN_URL', 'http://sso.local/loginPlain.php');
define('CFG_SSO_DISPLAY_NAME', 'Webgarden SSO Endpoint');

/* Facebook */
define('CFG_FB_APP_ID', '1707595419474201');
define('CFG_FB_APP_SECRET', '45b996d90b59818ee53d033781ea8be5');
define('CFG_FB_LOGIN_ENDPOINT', 'http://sso.local/facebookLogin.php');

global $loginMethodPriorities;
$loginMethodPriorities = array(
    'cors',
    'iframe',
    'noscript'
);
