<?php
namespace ModuleSSO;

use ModuleSSO\EndPoint\LoginMethod\HTTP as ELHTTP;
use ModuleSSO\Client\LoginHelper\HTTP;

use ModuleSSO\EndPoint\LoginMethod\Other as ELOther;
use ModuleSSO\Client\LoginHelper\Other;

use ModuleSSO\EndPoint\LoginMethod\ThirdParty as ELThirdParty;
use ModuleSSO\Client\LoginHelper\ThirdParty;

use ModuleSSO\Messages;

use Lcobucci\JWT\Signer\Keychain;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\Parser;

/**
 * Class Client
 * @package ModuleSSO
 */
class Client extends \ModuleSSO
{
    /** @var string $publicKey */
    private $publicKey = '';

    /** @var \ModuleSSO\Client\LoginHelper $loginHelper */
    private $loginHelper = null;

    /** @var array $MAP */
    private static $MAP = array(
        ELHTTP\NoScriptLogin::METHOD_NUMBER => '\ModuleSSO\Client\LoginHelper\HTTP\NoScriptHelper',
        ELHTTP\IframeLogin::METHOD_NUMBER => '\ModuleSSO\Client\LoginHelper\HTTP\IframeHelper',
        ELOther\CORSLogin::METHOD_NUMBER => '\ModuleSSO\Client\LoginHelper\Other\CORSHelper',
        ELThirdParty\FacebookLogin::METHOD_NUMBER => '\ModuleSSO\Client\LoginHelper\ThirdParty\FacebookHelper',
        ELThirdParty\GoogleLogin::METHOD_NUMBER => '\ModuleSSO\Client\LoginHelper\ThirdParty\GoogleHelper'
        
    );
    
    public function __construct($pubKeyPath = 'app/config/pk.pub')
    {
        $this->publicKey = file_get_contents($pubKeyPath);
    }

    /**
     * Method finds and return user from database based on ID provided in $_SESSION
     *
     * @uses $_SESSION
     * @uses \Database
     *
     * @return mixed If user is found, return array, otherwise null
     */
    public function getUser()
    {
        if($_SESSION['uid']) {
            $query = \Database::$pdo->prepare("SELECT * FROM users WHERE id = ?");
            $query->execute(array($_SESSION['uid']));
            return $query->fetch();
        } else {
            return null;
        }
        
    }

    /**
     * Returns requested URL if there is one, otherwise returns default CFG_DOMAIN_URL
     *
     * @return string
     */
    public function getContinueUrl()
    {
        //load server path from db
        $base = CFG_DOMAIN_URL;
        if(!empty($_SERVER['REQUEST_URI'])) {
            $rqu = $_SERVER['REQUEST_URI'];
            $result = parse_url($rqu);
            if(!empty($result['path'])) {
                $path = $result['path'];
                return  $base . $path;
            } else {
                return $base;
            }
        } else {
            return $base;
        }
    }
    
    public function setLoginHelper(Client\LoginHelper $loginHelper)
    {
        $this->loginHelper = $loginHelper;
    }

    /**
     * Sets $loginHelper according to parameter passed in $_GET
     * If there is no parameter, Client::$loginHelper is according to config file
     * Client::$loginHelper depends on capabilities of browser
     *
     * @link http://caniuse.com/#feat=cors
     *
     * @uses $_GET
     * @uses Client::$loginHelper
     * @uses ModuleSSO
     * @uses NoScriptLogin
     * @uses IframeLogin
     * @uses CORSLogin
     * @uses FacebookLogin
     * @uses GoogleLogin
     * @uses DirectLogin
     *
     */
    public function pickLoginHelper()
    {
        if(isset($_GET[\ModuleSSO::FORCED_METHOD_KEY])) {
            $key = $_GET[\ModuleSSO::FORCED_METHOD_KEY];
            if(isset(self::$MAP[$key])) {
                $class = self::$MAP[$key];
                $this->loginHelper = new $class();
            } else {
                $this->loginHelper = new HTTP\NoScriptHelper();
            }
            return;
        }
        
        //config
        global $loginHelperPriorities;
        foreach ($loginHelperPriorities as $method) {
            /** @var \ModuleSSO\Client\LoginHelper $loginHelper */
            $loginHelper = new $method;
            if($loginHelper->isSupported()) {
                 $this->loginHelper = $loginHelper;
                 break;
            }
        } 
    }

    public function appendScripts()
    {
        echo $this->loginHelper->appendScripts();
    }

    /**
     * Method for appending CSS styles to HTML
     *
     * @return string
     *
     * @uses LoginHelper::appendStyles()
     */
    public function appendStyles()
    {
        echo $this->loginHelper->appendStyles();
    }

    /**
     * Shows login form HTML of current loginHelper
     * @return string
     *
     * @uses LoginHelper::showLogin()
     */
    public function showLogin() {
        echo $this->loginHelper->showLogin($this->getContinueUrl());
    }

    /**
     * Waits for token given in $_GET, parses it and creates local context for user (logs user in)
     *
     * @uses $_GET
     * @uses ModuleSSO
     */
    private function tokenListener() {
        if(isset($_GET[\ModuleSSO::TOKEN_KEY])) {
            $urlToken = $_GET[\ModuleSSO::TOKEN_KEY];
            try {
                $token = (new Parser())->parse((string) $urlToken);
                $signer = new Sha256();
                $keychain = new Keychain();

                //check if token is signed and not expired
                if($token->verify($signer, $keychain->getPublicKey($this->publicKey)) && $token->getClaim('exp') > time()) {
                    $query = \Database::$pdo->prepare("SELECT * FROM tokens WHERE value = '$urlToken' AND used = 0");
                    $query->execute();
                    $dbtoken = $query->fetch();                          
                    if($dbtoken) {
                        $query = \Database::$pdo->prepare("SELECT * FROM users WHERE id = ?");
                        $query->execute(array($token->getClaim('uid')));
                        $user = $query->fetch();
                        if($user) {
                            $query = \Database::$pdo->prepare("UPDATE tokens SET used = 1 WHERE value = '$urlToken'");
                            $query->execute();
                            
                            $_SESSION['uid'] = $user['id'];
                            header("Location: " .  $this->getContinueUrl());
                            exit();
                        }
                    }
                } else {
                    Messages::insert('Login failed, please try again', 'warn');
                    header("Location: " .  $this->getContinueUrl());
                    exit();
                }
            } catch (\Exception $e) {
                Messages::insert('Login failed, please try again', 'warn');
                header("Location: " .  $this->getContinueUrl());
                exit();
            }
            
        }
    }

    /**
     * Handles local logout and SSO (global) logout
     * Redirects user to specific logout URL
     */
    private function logoutListener() {
        if(isset($_GET[\ModuleSSO::LOGOUT_KEY]) && $_GET[\ModuleSSO::LOGOUT_KEY] == 1) {
            unset($_SESSION["uid"]);
            header("Location: " . CFG_DOMAIN_URL);
            exit();
        } else if(isset($_GET[\ModuleSSO::GLOBAL_LOGOUT_KEY]) && $_GET[\ModuleSSO::GLOBAL_LOGOUT_KEY] == 1) {
            unset($_SESSION["uid"]);
            header("Location: " . CFG_SSO_ENDPOINT_URL . '?' . \ModuleSSO::LOGOUT_KEY . '=1&' . \ModuleSSO::CONTINUE_KEY . '=' . CFG_DOMAIN_URL);
            exit();
        }
    }

    /**
     * Starts lifecycle of Client
     *
     * @uses Client::tokenListener()
     * @uses Client::logoutListener()
     */
    public function run()
    {
        $this->tokenListener();
        $this->logoutListener();
    }
}