<?php
define('CFG_SQL_HOST', 'localhost');
define('CFG_SQL_DBNAME', 'sso');
define('CFG_SQL_USERNAME', 'root');
define('CFG_SQL_PASSWORD', 'root');
define('CFG_JWT_ISSUER', 'sso.local');
define('CFG_SSO_ENDPOINT_URL', 'http://sso.local/login.php');
define('CFG_SSO_ENDPOINT_PLAIN_URL', 'http://sso.local/loginPlain.php');
define('CFG_SSO_DISPLAY_NAME', 'Webgarden SSO Endpoint');

/* Facebook */
define('CFG_FB_APP_ID', '1707595419474201');
define('CFG_FB_APP_SECRET', '45b996d90b59818ee53d033781ea8be5');
define('CFG_FB_LOGIN_ENDPOINT', 'http://sso.local/facebookLogin.php');

/* Google Plus */
define('CFG_G_CLIENT_ID', '851847042686-3n51ffn8p9jhnc83e50fve62tc7uaokq.apps.googleusercontent.com');
define('CFG_G_CLIENT_SECRET', 'd5izoJR_c9o00xV9zmfOvYcx');
define('CFG_G_REDIRECT_URI', 'http://localhost/googleLogin.php');

global $loginHelperPriorities;
$loginHelperPriorities = array(
    '\ModuleSSO\Client\LoginHelper\Other\CORSHelper',
    '\ModuleSSO\Client\LoginHelper\HTTP\IframeHelper',
    '\ModuleSSO\Client\LoginHelper\HTTP\NoScriptHelper'
);

//CORS supported browsers
//http://caniuse.com/#feat=cors
global $CORSBrowsers;
$CORSBrowsers = array(
    'chrome' => 31,
    'ie' => 10,
    'edge' => 12,
    'firefox' => 37,
    'safari' => 6.1,
    'opera' => 12.1, 
);