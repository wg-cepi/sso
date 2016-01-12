<?php
require_once __DIR__ .'/../config/config.inc.php';
require 'AntiCSRF.php';
require_once 'browserSniffer.php';

use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Keychain;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\Parser;

class Cookie
{
    /*
     * @var SSOC Bezpecnostni SSO cookie nutna k rozpoznani uzivatele
     * Vypocita se jako sha1(browser fingerprint) . sha1(user_id)
     */
    const SSOC = 'SSSOC';
    
    public static function generateHash($userId)
    {
        return base64_encode(sha1(BrowserSniffer::getFingerprint()) . sha1($userId)); 
    }
    public static function verifyHash($hash, $userId)
    {
        if($hash === self::generateHash($userId)) {
            return true;
        } else {
            return false;
        }
    }
}
abstract class ModuleSSO
{
    const TOKEN_KEY = 'token';
    const RELOG_KEY = 'relog';
    const LOGIN_KEY = 'login';
    const CSRF_KEY = 'anti_csrf';
    const DOMAIN_KEY = 'd';
    const METHOD_KEY = 'm';
    const CONTINUE_KEY = 'continue';
    const FORCED_METHOD_KEY = 'f';
    
    abstract public function run();  
}

class Client extends ModuleSSO
{
    private $returnUrl = '';
    
    public function __construct($returnUrl)
    {
        $this->returnUrl = $returnUrl;
    }
    
    public function getContinueUrl()
    {
        //load server path from db
        $base = CFG_DOMAIN_URL;
        $rqu = "";
        if(!empty($_SERVER['REQUEST_URI']) && $_SERVER['REQUEST_URI'] != "/logout.php") {
            $rqu = $_SERVER['REQUEST_URI'];
            $result = parse_url($rqu);
            $path = "";
            if(isset($result['path'])) {
                $path = $result['path'];
                return  $base . $path;
            } else {
                return $base;
            }
        } else {
            return $base;
        }
    }
    
    public function pickLoginMethod()
    {
        if(isset($_GET[ModuleSSO::FORCED_METHOD_KEY]) && $_GET[ModuleSSO::FORCED_METHOD_KEY] == 1) {
            NoScriptLogin::clientHTML($this->getContinueUrl());
            return;
        }
        //CORS supported browsers
        //http://caniuse.com/#feat=cors
        $supportedBrowsers = array(
            'chrome' => 31,
            'ie' => 10,
            'edge' => 12,
            'firefox' => 37,
            'safari' => 6.1,
            'opera' => 12.1, 
        );
        
        //config
        global $loginMethodPriorities;
        foreach ($loginMethodPriorities as $method) {
            if($method === 'cors') {
                $browser = new BrowserSniffer();
                if(isset($supportedBrowsers[$browser->getName()])) {
                    if($browser->getVersion() >= $supportedBrowsers[$browser->getName()]) {
                        CORSLogin::clientHTML($this->getContinueUrl());
                        break;
                    }
                }   
            }
            else if($method === 'iframe') {
                IframeLogin::clientHTML($this->getContinueUrl());
                break;
            }
            else if($method === 'noscript') {
                NoScriptLogin::clientHTML($this->getContinueUrl());
                break;
            }
        } 
    }
    
    //todo continue URL
    public function login() {
        if(isset($_GET[ModuleSSO::TOKEN_KEY])) {
            $urlToken = $_GET[ModuleSSO::TOKEN_KEY];

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
                    header("Location: " .  $this->returnUrl);
                }
            }  
        } else {
            //prompt bad login
        }
    }
    
    public function run()
    {
        $this->login();
    }    
}

class EndPoint extends ModuleSSO
{
    /**
     * @var LoginMethod $loginMethod
     */
    public $loginMethod = null;
    
    public function run()
    {
        if(isset($_GET[ModuleSSO::METHOD_KEY])) {
            $method = $_GET[ModuleSSO::METHOD_KEY];
            if($method == 1) {
                $this->loginMethod = new NoScriptLogin();
                $this->loginMethod->run();
            } else if($method == 2) {
                $this->loginMethod = new IframeLogin();
                $this->loginMethod->run();
            } else if($method == 3) {
                $this->loginMethod = new CORSLogin();
                $this->loginMethod->run();
            } else {
                //TODO if method not 1,2,3?
            }
        } else {
            $this->loginMethod = new DirectLogin();
            $this->loginMethod->run();
        }
    }
    
}

class JWT
{
    public $issuer = null;
    public $audience = null;
    public $expiration = null;
    public $notBefore = null;
    public $issuedAt = null;
    
    public $token = null;
    
    private $privateKey = null;
    private $publicKey = null;
    
    public function __construct()
    {
        //todo load $issuer, $expiration, $notBefore from config
        
        $this->privateKey = file_get_contents(__DIR__ . '/../config/pk.pem');
        $this->issuer = CFG_JWT_ISSUER;
        $this->expiration = time() + 3600;
        $this->issuedAt = time();
        $this->notBefore = time() + 60;
    }
    
    /**
     * Generates and return JWT based on input values and config variables
     * @param array $values
     * @return string $token
     */
    public function generate($values)
    {
        $signer = new Sha256();
        $keychain = new Keychain();
        
        $builder = new Builder();
        if($this->issuer !== null) {
            $builder->setIssuer($this->issuer);
        }
        if($this->audience !== null) {
            $builder->setAudience($this->audience);
        }
        if($this->notBefore !== null) {
            $builder->setNotBefore($this->notBefore);
        }
        if($this->issuedAt !== null) {
            $builder->setIssuedAt($this->issuedAt);
        }
        
        foreach ($values as $name => $value)
        {
            $builder->set($name, $value);
        }
        
        $token = $builder->sign($signer, $keychain->getPrivateKey($this->privateKey)) // Creates a signature using your private key
                ->getToken(); // Retrieves the generated token

        $this->token = $token;
        return $token;
    }
}

class ContinueUrl
{  
    public function __construct()
    {        
        // this class will probably use static methods
    }
    
    public function getUrl()
    {
        if(isset($_GET[ModuleSSO::CONTINUE_KEY])) {
            $url = $_GET[ModuleSSO::CONTINUE_KEY];
            $parsed = parse_url($url);
            if(!empty($parsed['host'])) {
                if($this->isInWhitelist($parsed['host'])) {
                    return $url;
                }
            } else {
                return CFG_SSO_ENDPOINT_URL;
            }
        } else if(isset($_SERVER['HTTP_REFERER'])) {
            $url = $_SERVER['HTTP_REFERER'];
            $parsed = parse_url($url);
            if(!empty($parsed['host'])) {
                if($this->isInWhitelist($parsed['host'])) {
                    return $url;
                }
            } else {
                return CFG_SSO_ENDPOINT_URL;
            }
        } else {
            return CFG_SSO_ENDPOINT_URL;
        }  
    }
    
    public function isInWhitelist($domainName)
    {
        $query = Database::$pdo->prepare("SELECT * FROM domains WHERE name = '$domainName'");
        $domain = $query->execute();
        if($domain) {
            return true;
        } else {
            return false;
        }
    }
}

interface ILoginMethod
{
    public function login();
}

abstract class LoginMethod implements ILoginMethod
{   
    public $domain = '';
    public $continueUrl = '';
    
    public function setCookies($userId)
    { 
        $time = time();
        $ssoCookie = Cookie::generateHash($userId);
        setcookie(Cookie::SSOC, $ssoCookie, null, null, null, null, true);
        
        $query = Database::$pdo->prepare("UPDATE users SET cookie = '$ssoCookie',logged = '$time' WHERE id = $userId");
        $query->execute();
        
    }
    
    public function getUserFromCookie()
    {
        if(isset($_COOKIE[Cookie::SSOC])) {
            $ssoCookie = $_COOKIE[Cookie::SSOC];
            $query = Database::$pdo->prepare("SELECT * FROM users WHERE cookie = '$ssoCookie'");
            $query->execute();
            $user = $query->fetch();
            if($user) {
                $userId = $user['id'];
                $userCookie = $user['cookie'];
                if(Cookie::verifyHash($userCookie, $userId)) {
                    return $user;
                } else {
                    return null;
                }
                
            } else {
                //throw new \Exception("Cookie user not found");
                return null;
            }
        } else {
            return null;
        }
    }
    
    public function run()
    {
        $this->login();
    }
    
}

class NoScriptLogin extends LoginMethod
{   
    public static function clientHTML($continue)
    {
        echo '<form method="get" action="'. CFG_SSO_ENDPOINT_URL .'">
                <input type="hidden" name="' . ModuleSSO::CONTINUE_KEY . '" value="' . $continue . '"/>
                <input type="hidden" name="' . ModuleSSO::METHOD_KEY . '" value="1"/>
                <input type="hidden" name="' . ModuleSSO::CSRF_KEY . '" value="' . AntiCSRF::generate(CFG_DOMAIN_NAME) . '"/>
                <input type="hidden" name="' . ModuleSSO::DOMAIN_KEY . '" value="' . CFG_DOMAIN_NAME . '"/>
                <input type="submit" value="Login with SSO"/>
            </form>';
        
    }
    
    public function redirect($url, $code = 302)
    {
        http_response_code($code);
        header("Location: " . $url);
        exit;
    }
    
    public function showHTML()
    {
        $user = $this->getUserFromCookie();
        if($user !== null) {
            echo $this->showHTMLUserInfo($user);
        } else {
            echo $this->showHTMLLoginForm();
        }
    }
    
    public function showHTMLLoginForm()
    {
        return '<form method="get">
                <label>
                    Email:<input type="text" name="email"/>
                </label>
                <br>
                <label>
                    Password:<input type="password" name="password"/>
                </label>
                <br>
                <input type="hidden" name="' . ModuleSSO::CONTINUE_KEY . '" value="' . $this->continueUrl .  '"/>
                <input type="hidden" name="' . ModuleSSO::METHOD_KEY . '" value="1"/>
                <input type="hidden" name="' . ModuleSSO::DOMAIN_KEY . '" value="' . $this->domain . '"/>
                <input type="hidden" name="' . ModuleSSO::CSRF_KEY . '" value="' . AntiCSRF::generate($this->domain) . '"/>
                <input type="submit" value="Login"/>
           </form>';
        
    }
    
    public function showHTMLUserInfo($user)
    {
        $csrfToken = AntiCSRF::generate($this->domain);
        $html = '<div>
               <p>You are logged in as <strong>' . $user['email'] . '</strong></p>
               <ul>';
               if ($this->continueUrl !== CFG_SSO_ENDPOINT_URL) {
                   $src = CFG_SSO_ENDPOINT_URL . '?' . ModuleSSO::METHOD_KEY . '=1&login=1&' . ModuleSSO::CONTINUE_KEY . '=' . $this->continueUrl . '&' . ModuleSSO::CSRF_KEY . '=' . $csrfToken . '&' . ModuleSSO::DOMAIN_KEY . '=' . $this->domain;
                   $html .= '<li><a href="' . $src . '" title="Continue as ' . $user['email'] . '"> Continue as ' . $user['email'] . '</a></li>';
               }
               $src = CFG_SSO_ENDPOINT_URL . '?' . ModuleSSO::METHOD_KEY . '=1&relog=1&' . ModuleSSO::CONTINUE_KEY . '=' . $this->continueUrl . '&' . ModuleSSO::CSRF_KEY . '=' . $csrfToken . '&' . ModuleSSO::DOMAIN_KEY . '=' . $this->domain;
               $html .= '<li><a href="' . $src . '" title="Log in as another user">Log in as another user</a>
               </ul>
           </div>';
        return $html;
        
    }
    
    public function login()
    {
        $this->continueUrl = (new ContinueUrl())->getUrl();
        //if no token, stop login process
        if(!isset($_GET[ModuleSSO::CSRF_KEY]) || !AntiCSRF::check($_GET[ModuleSSO::CSRF_KEY])){
            $this->redirect($this->continueUrl);
            return;
        }
        
        //domain
        if(isset($_GET[ModuleSSO::DOMAIN_KEY])){
            $this->domain = $_GET[ModuleSSO::DOMAIN_KEY];
        } else {
            $this->redirect($this->continueUrl);
            return;
        }

        $this->continueUrl = (new ContinueUrl())->getUrl();        
        if(isset($_GET['email']) && isset($_GET['password'])) {
            $email =  $_GET['email'];
            $password =  $_GET['password'];

            $query = Database::$pdo->prepare("SELECT * FROM users WHERE email = ? AND password = ?");
            $query->execute(array($email, $password));
            $user = $query->fetch();
            if($user) {
                $this->setCookies($user['id']);
                $token = (new JWT())->generate(array('uid' => $user['id']));
                $url = $this->continueUrl .  "?" . ModuleSSO::TOKEN_KEY . "=" . $token;
                $this->redirect($url);
            } else {
                echo $this->showHTMLLoginForm();
            }
        } else if(isset($_GET['login'])) {
            if(isset($_COOKIE[Cookie::SSOC])) {
                $user = $this->getUserFromCookie();
                if($user) {
                    $token = (new JWT())->generate(array('uid' => $user['id']));
                    $url = $this->continueUrl .  "?" . ModuleSSO::TOKEN_KEY . "=" . $token;
                    $this->redirect($url);
                } else {
                    echo $this->showHTMLLoginForm();
                }
            } else {
                echo $this->showHTMLLoginForm();
            }
        }
        else if (isset($_GET['relog'])){
            echo $this->showHTMLLoginForm();
        } else {
            $this->showHTML();
        }
    }
}

class IframeLogin extends LoginMethod
{   
    public static function clientHTML($continue)
    {
        $src = CFG_SSO_ENDPOINT_URL . '?' . ModuleSSO::METHOD_KEY . '=2&' . ModuleSSO::CONTINUE_KEY . '=' . $continue . '&' . ModuleSSO::CSRF_KEY . '=' . AntiCSRF::generate(CFG_DOMAIN_NAME) . '&' . ModuleSSO::DOMAIN_KEY . '=' . CFG_DOMAIN_NAME;
        echo "<div>";
        echo '<iframe src="'. $src . '" frameborder="0"></iframe>';
        echo "</div>";
    }
    
    public function redirect($url)
    {
        echo "<script>window.parent.location = '" . $url . "';</script>";
    }
    
    public function showHTML()
    {
        $user = $this->getUserFromCookie();
        if($user !== null) {
            echo $this->showHTMLUserInfo($user);
        } else {
            echo $this->showHTMLLoginForm();
        }
    }
    
    public function showHTMLLoginForm()
    {
        return '<form method="get">
                <label>
                    Email:<input type="text" name="email"/>
                </label>
                <br>
                <label>
                    Password:<input type="password" name="password"/>
                </label>
                <br>
                <input type="hidden" name="' . ModuleSSO::CONTINUE_KEY . '" value="' . $this->continueUrl .  '"/>
                <input type="hidden" name="' . ModuleSSO::METHOD_KEY . '" value="2"/>
                <input type="hidden" name="' . ModuleSSO::DOMAIN_KEY . '" value="' . $this->domain . '"/>
                <input type="hidden" name="' . ModuleSSO::CSRF_KEY . '" value="' . AntiCSRF::generate($this->domain) . '"/>
                <input type="submit" value="Login"/>
           </form>';
        
    }
    
    public function showHTMLUserInfo($user)
    {
        $html = '<div>
                <p>You are logged in as <strong>' . $user['email'] . '</strong></p>
                <ul>';
                $csrfToken = AntiCSRF::generate($this->domain);
                if ($this->continueUrl !== CFG_SSO_ENDPOINT_URL) {
                    $src = CFG_SSO_ENDPOINT_URL . '?' . ModuleSSO::METHOD_KEY . '=2&relog=1&' . ModuleSSO::CONTINUE_KEY . '=' . $this->continueUrl . '&' . ModuleSSO::CSRF_KEY . '=' . $csrfToken . '&' . ModuleSSO::DOMAIN_KEY . '=' . $this->domain;
                    $html .= '<li><a href="' . $src . '" title="Continue as ' . $user['email'] . '"> Continue as ' . $user['email'] . '</a></li>';
                }
                $src = CFG_SSO_ENDPOINT_URL . '?' . ModuleSSO::METHOD_KEY . '=1&login=1&' . ModuleSSO::CONTINUE_KEY . '=' . $this->continueUrl . '&' . ModuleSSO::CSRF_KEY . '=' . $csrfToken . '&' . ModuleSSO::DOMAIN_KEY . '=' . $this->domain;
                $html .= '<li><a href="' . $src . '" title="Log in as another user">Log in as another user</a>
                </ul>
           </div>';
        return $html;
        
    }
    
    public function tokenMismatch()
    {
        echo '<a href="' . $this->continueUrl . '">Token mismatch, continue here</a>';  
    }
    
    public function login()
    {
        $this->continueUrl = (new ContinueUrl())->getUrl();
        //if no token, stop login process
        if(!isset($_GET[ModuleSSO::CSRF_KEY]) || !AntiCSRF::check($_GET[ModuleSSO::CSRF_KEY])){
            $this->redirect($this->continueUrl);
            return;
        }
        
        //domain
        if(isset($_GET[ModuleSSO::DOMAIN_KEY])){
            $this->domain = $_GET[ModuleSSO::DOMAIN_KEY];
        } else {
            $this->redirect($this->continueUrl);
            return;
        }
        
        if(isset($_GET['email']) && isset($_GET['password'])) {
            $email =  $_GET['email'];
            $password =  $_GET['password'];

            $query = Database::$pdo->prepare("SELECT * FROM users WHERE email = ? AND password = ?");
            $query->execute(array($email, $password));
            $user = $query->fetch();
            if($user) {
                $this->setCookies($user['id']);
                $token = (new JWT())->generate(array('uid' => $user['id']));

                $url = $this->continueUrl .  "?" . ModuleSSO::TOKEN_KEY . "=" . $token;
                $this->redirect($url);
            } else {
                echo $this->showHTMLLoginForm();
            }
        } else if(isset($_GET['login'])) {
            if(isset($_COOKIE[Cookie::SSOC])) {
                $user = $this->getUserFromCookie();
                if($user) {
                    $token = (new JWT())->generate(array('uid' => $user['id']));
                    $url = $this->continueUrl .  "?" . ModuleSSO::TOKEN_KEY . "=" . $token;
                    $this->redirect($url);
                } else {
                    echo $this->showHTMLLoginForm();
                }
            } else {
                echo $this->showHTMLLoginForm();
            }
        }
        else if (isset($_GET['relog'])){
            echo $this->showHTMLLoginForm();
        } else {
            $this->showHTML();
        }
    }
}

class CORSLogin extends LoginMethod
{
    public static function clientHTML()
    {
        //echo "<script src='http://code.jquery.com/jquery-2.1.4.min.js'></script>";
        echo "<script src='http://sso.local/app/module_sso/prototype.js'></script>";
        echo "<script src='http://sso.local/app/module_sso/cors.js'></script>";
        echo '<div id="id-login-area">';
        echo '<form id="id-sso-form" method="GET" action="' . CFG_SSO_ENDPOINT_URL . '">'
                . '<label>Email:<input type="text" name="email"/></label><br/>'
                . '<label>Password:<input type="password" name="password"/></label><br/>'
                . '<input type="submit" id="id-login-button" value="login"/>'
            . '</form>';
        echo '</div>';
    }
    public function checkCookie()
    {
        header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
        header('Access-Control-Allow-Credentials: true');
        header('Content-Type: application/json');
        if(!isset($_COOKIE[Cookie::SSOC])) {
            echo json_encode(array("status" => "no_cookie"));
        } else {
            $ssoCookie = $_COOKIE[Cookie::SSOC];
            $query = Database::$pdo->prepare("SELECT * FROM users WHERE cookie = '$ssoCookie'");
            $query->execute(array());
            $user = $query->fetch();
            if($user) {
                $token = (new JWT())->generate(array('uid' => $user['id']));
                echo '{"status": "ok", "' . ModuleSSO::TOKEN_KEY . '": "' . $token . '", "email": "' . $user['email'] .'"}';
            } else {
                //bad cookie, user not found
            }
        }
    }
    
    public function login()
    {
        header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
        header('Content-Type: application/json');
        header('Access-Control-Allow-Credentials: true');

        if(!empty($_GET['email']) && !empty($_GET['password'])) {
            $query = Database::$pdo->prepare("SELECT * FROM users WHERE email = ? AND password = ?");
            $query->execute(array($_GET['email'], $_GET['password']));
            $user = $query->fetch();
            if($user) {
                $this->setCookies($user['id']);
                $token = (new JWT())->generate(array('uid' => $user['id']));

                echo '{"status": "ok", "' . ModuleSSO::TOKEN_KEY . '": "' . $token . '"}';
            } else {
                echo json_encode(array("status" => "fail"));
            }
        } else {
            echo json_encode(array("status" => "bad_login"));
        }

        
    }
    
    public function run()
    {
        if(isset($_SERVER['HTTP_ORIGIN'])){
            $query = Database::$pdo->prepare("SELECT * FROM domains WHERE name = '" . $_SERVER['HTTP_ORIGIN'] . "'");
            $domain = $query->execute();
            if($domain) {
                if(isset($_GET['login']) && $_GET['login'] == 1) {
                    $this->login();
                } else if(isset($_GET['checkCookie']) && $_GET['checkCookie'] == 1) {
                    $this->checkCookie();
                }
            }
        }
        
    }
}

class DirectLogin extends LoginMethod
{
    public function login()
    {
        if(isset($_GET['email']) && isset($_GET['password'])) {
             $email =  $_GET['email'];
             $password =  $_GET['password'];

             $query = Database::$pdo->prepare("SELECT * FROM users WHERE email = ? AND password = ?");
             $query->execute(array($email, $password));
             $user = $query->fetch();
             if($user) {
                 $this->setCookies($user['id']);
                 $this->redirect();
             } else {
                 echo $this->showHTMLLoginForm();
             }
         }
         else if (isset($_GET['relog'])){
             echo $this->showHTMLLoginForm();
         } else {
             $this->showHTML();
         }
    }
    
    public function showHTML()
    {
        $user = $this->getUserFromCookie();
        if($user !== null) {
            echo $this->showHTMLUserInfo($user);
        } else {
            echo $this->showHTMLLoginForm();
        }
    }
    
    public function redirect()
    {
        http_response_code(302);
        header("Location: " . CFG_SSO_ENDPOINT_URL);
        exit;

    }
    
    public function showHTMLLoginForm()
    {
        return '<form method="get">
                <label>
                    Email:<input type="text" name="email"/>
                </label>
                <br>
                <label>
                    Password:<input type="password" name="password"/>
                </label>
                <br>
                <input type="submit" value="Login"/>
           </form>';
    }
    
    public function showHTMLUserInfo($user)
    {
        $html = '<div>
               <p>You are logged in as <strong>' . $user['email'] . '</strong></p>
               <ul>';
               $html .= '<li><a href="' . CFG_SSO_ENDPOINT_URL . '?relog=1" title="Log in as another user">Log in as another user</a>
               </ul>
           </div>';
        return $html;
    }
}