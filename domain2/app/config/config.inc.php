<?php
require_once 'vendor/autoload.php';

define('CFG_SQL_HOST', 'localhost');
define('CFG_SQL_DBNAME', 'sso');
define('CFG_SQL_USERNAME', 'root');
define('CFG_SQL_PASSWORD', '');
define('CFG_JWT_AUD', 'domain2.local');
define('CFG_AUTH_SERVER', 'sso.local');

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

use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Keychain;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\Parser;

function parseToken() {
    if(isset($_GET['token'])) {
        $urlToken = $_GET['token'];

        $pubkey = file_get_contents('app/config/pk.pub');
        $token = (new Parser())->parse((string) $urlToken); // Parses from a string
        $signer = new Sha256();
        $keychain = new Keychain();
        if($token->verify($signer, $keychain->getPublicKey($pubkey))) {
            $query = Database::$pdo->prepare("SELECT * FROM users WHERE id = ?");
            $query->execute(array($token->getClaim('uid')));
            $user = $query->fetch();
            if($user) {
                $_SESSION['uid'] = $user['id'];
                header("Location: http://" . CFG_JWT_AUD);
            }
        }  
    }
}

function getContinuePath() {
    $rqu = (!empty($_SERVER['REQUEST_URI']) && $_SERVER['REQUEST_URI'] != "/logout.php") ? $_SERVER['REQUEST_URI'] : "";
    $result = parse_url($rqu);
    $path = "";
    if(isset($result['path'])) {
        $path = $result['path'];
    }
    return $path;
}



