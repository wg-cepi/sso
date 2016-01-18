<?php
require_once __DIR__ . '/../../vendor/autoload.php';
//require_once __DIR__ . '/../Mo/browserSniffer.php';


require_once __DIR__ . '/../ModuleSSO/JWT.php';
require_once __DIR__ . '/../ModuleSSO/Cookie.php';
require_once __DIR__ . '/../ModuleSSO/BrowserSniffer.php';

require_once __DIR__ . '/../ModuleSSO/ModuleSSO.php';
require_once __DIR__ . '/../ModuleSSO/Client.php';
require_once __DIR__ . '/../ModuleSSO/EndPoint.php';

require_once __DIR__ . '/../ModuleSSO/method/ILoginMethod.php';
require_once __DIR__ . '/../ModuleSSO/method/LoginMethod.php';

require_once __DIR__ . '/../ModuleSSO/method/classic/ClassicLogin.php';
require_once __DIR__ . '/../ModuleSSO/method/classic/NoScriptLogin.php';
require_once __DIR__ . '/../ModuleSSO/method/classic/IframeLogin.php';
require_once __DIR__ . '/../ModuleSSO/method/classic/DirectLogin.php';

require_once __DIR__ . '/../ModuleSSO/method/experimental/CORSLogin.php';

require_once __DIR__ . '/../ModuleSSO/method/3rd-party/ThirdPartyLogin.php';
require_once __DIR__ . '/../ModuleSSO/method/3rd-party/FacebookLogin.php';

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


$loginMethodPriorities = array(
    'cors',
    'iframe',
    'noscript'
);

class Database {
    public static $pdo = null;
    public static function init() {
        $dsn = 'mysql:dbname=' . CFG_SQL_DBNAME . ';host=' . CFG_SQL_HOST . '';
        $user = CFG_SQL_USERNAME;
        $password = CFG_SQL_PASSWORD;
        
        try {
            $pdo = new PDO($dsn, $user, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$pdo = $pdo;
        } catch (PDOException $e) {
            die('Connection failed: ' . $e->getMessage());
        }
    }
}
Database::init();

class Logger {
    public static function log($what, $path = 'C:/wamp/logs/ssoLog.txt') {
        $fp = fopen($path, "a+");
        fwrite($fp, print_r($what, true). "\n");
        fclose($fp);
    }
}

function redirect($url, $code = 303) {
    http_response_code($code);
    header("Location: " . $url);
    exit;
}
