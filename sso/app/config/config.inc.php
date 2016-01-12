<?php
require_once __DIR__ . '/../../vendor/autoload.php';

define('CFG_SQL_HOST', 'localhost');
define('CFG_SQL_DBNAME', 'sso');
define('CFG_SQL_USERNAME', 'root');
define('CFG_SQL_PASSWORD', '');
define('CFG_JWT_ISSUER', 'sso.local');
define('CFG_SSO_ENDPOINT_URL', 'http://sso.local/login.php');

$whiteList = array("http://domain1.local", "http://domain2.local", 'http://sso.local');

$loginMethodPriorities = array(
    //'cors',
    //'iframe',
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
