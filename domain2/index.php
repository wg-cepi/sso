<?php
session_start();
require_once 'vendor/autoload.php';
require_once 'app/config/config.inc.php';

use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Keychain;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\Parser;

Database::init();
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
            header("Location: http://domain2.local/");
        }
    }
    
    
    
}

$rqu = (!empty($_SERVER['REQUEST_URI']) && $_SERVER['REQUEST_URI'] != "/logout.php") ? $_SERVER['REQUEST_URI'] : "";
$continue = 'http://' . $_SERVER['HTTP_HOST'] . $rqu;
echo "<h1>Domain 2</h1>";

if(isset($_SESSION['uid'])) {
    echo "UID: " . $_SESSION['uid'];
    echo '<a href="./logout.php">Logout</a>';
} else {
    echo '<iframe src="' . CFG_AUTH_SERVER . '/jwt.php?continue=' . $continue . '" frameborder="0"></iframe>';
}
