<?php
require_once 'vendor/autoload.php';

define('CFG_SQL_HOST', 'localhost');
define('CFG_SQL_DBNAME', 'sso');
define('CFG_SQL_USERNAME', 'root');
define('CFG_SQL_PASSWORD', '');
define('CFG_JWT_ISSUER', 'sso.local/');

$whiteList = array("http://domain1.local", "http://domain2.local", 'http://sso.local');


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

use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Keychain;
use Lcobucci\JWT\Signer\Rsa\Sha256;

function generateJWT($uid, $aud) {
    $pk = file_get_contents(__DIR__ . '/pk.pem');
    $signer = new Sha256();
    $keychain = new Keychain();
    $token = (new Builder())->setIssuer(CFG_JWT_ISSUER) // Configures the issuer (iss claim)
                            ->setAudience($aud) // Configures the audience (aud claim)
                            //->setId('4f1g23a12aa', true) // Configures the id (jti claim), replicating as a header item
                            ->setIssuedAt(time()) // Configures the time that the token was issue (iat claim)
                            ->setNotBefore(time() + 60) // Configures the time that the token can be used (nbf claim)
                            ->setExpiration(time() + 3600) // Configures the expiration time of the token (exp claim)
                            ->set('uid', $uid) // Configures a new claim, called "uid"
                            ->sign($signer,  $keychain->getPrivateKey($pk)) // creates a signature using your private key
                            ->getToken(); // Retrieves the generated token
    return $token;
}

function getContinueUrl($url) {
    $result = parse_url($url);
    $path = $host = "";
    if(isset($result['path']) && isset($result['host'])) {
        $host = $result['host'];
        $path = $result['path'];
    }
    return $host . $path;
}

function getContinue(){
    $continue = "";
    if(!empty($_GET['continue'])) {
        $continue = $_GET['continue'];
        $_SESSION['continue'] = $continue;
    } else if(isset($_SEVER['HTTP_HOST']) && isset($_SERVER['REQUEST_URI'])) {
        $result = parse_url($_SERVER['REQUEST_URI']);
        $path = "";
        if(isset($result['path'])) {
            $path = $result['path'];
        }
        $continue = CFG_JWT_ISSUER . $path;
        $_SESSION['continue'] = $continue;
    } else {
        if(!isset($_SESSION['continue'])) {
            $continue = CFG_JWT_ISSUER . "/login.php";
        } else {
            $continue = $_SESSION['continue'];
        }
    }
    return $continue;
}
