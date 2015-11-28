<?php

define('CFG_SQL_HOST', 'localhost');
define('CFG_SQL_DBNAME', 'sso');
define('CFG_SQL_USERNAME', 'root');
define('CFG_SQL_PASSWORD', '');
define('CFG_JWT_ISSUER', 'http://sso.localhost/');

$whiteList = array("http://domain1.local", "http://domain2.local");

require_once 'vendor/autoload.php';

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

