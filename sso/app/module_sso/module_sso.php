<?php
require_once __DIR__ .'/../config/config.inc.php';

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
    const METHOD_KEY = 'm';
    const CONTINUE_KEY = 'continue';
    const FORCED_METHOD_KEY = 'f';
    const LOGOUT_KEY = 'logout';
    const GLOBAL_LOGOUT_KEY = 'glogout';
    
    abstract public function run();  
}

class Client extends ModuleSSO
{
    private $publicKey = '';
    private $loginMethod = null;
    
    public function __construct($pubKeyPath = 'app/config/pk.pub')
    {
        $this->publicKey = file_get_contents($pubKeyPath);
    }
    
    public function getContinueUrl()
    {
        //load server path from db
        $base = CFG_DOMAIN_URL;
        $rqu = "";
        if(!empty($_SERVER['REQUEST_URI'])) {
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
        if(isset($_GET[ModuleSSO::FORCED_METHOD_KEY])) {
            $m = $_GET[ModuleSSO::FORCED_METHOD_KEY];
            if($m == NoScriptLogin::METHOD_NUMBER) {
                $this->loginMethod = new NoScriptLogin();
            } else if($m == IframeLogin::METHOD_NUMBER) {
                $this->loginMethod = new IframeLogin();
            } else if($m == CORSLogin::METHOD_NUMBER) {
                $this->loginMethod = new CORSLogin();
            } else {
                 $this->loginMethod = new NoScriptLogin();
            }
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
                        $this->loginMethod = new CORSLogin();
                        break;
                    }
                }   
            }
            else if($method === 'iframe') {
                $this->loginMethod = new IframeLogin();
                break;
            }
            else if($method === 'noscript') {
                $this->loginMethod = new NoScriptLogin();
                break;
            }
        } 
    }
    
    public function login() {
        if(isset($_GET[ModuleSSO::TOKEN_KEY])) {
            $urlToken = $_GET[ModuleSSO::TOKEN_KEY];
;
            $token = (new Parser())->parse((string) $urlToken);
            $signer = new Sha256();
            $keychain = new Keychain();
            
            try {
                if($token->verify($signer, $keychain->getPublicKey($this->publicKey))) {
                    $query = Database::$pdo->prepare("SELECT * FROM users WHERE id = ?");
                    $query->execute(array($token->getClaim('uid')));
                    $user = $query->fetch();
                    if($user) {
                        $_SESSION['uid'] = $user['id'];
                        header("Location: " .  $this->getContinueUrl());
                    }
                } else {
                    echo $this->showHTMLLoginFailed();
                }
            } catch (Exception $e) {
                echo $this->showHTMLLoginFailed();
            }
            
        }
    }
    
    public function logout() {
        if(isset($_GET[ModuleSSO::LOGOUT_KEY]) && $_GET[ModuleSSO::LOGOUT_KEY] == 1) {
            unset($_SESSION["uid"]);
            header("Location: " . CFG_DOMAIN_URL);
        } else if(isset($_GET[ModuleSSO::GLOBAL_LOGOUT_KEY]) && $_GET[ModuleSSO::GLOBAL_LOGOUT_KEY] == 1) {
             unset($_SESSION["uid"]);
             header("Location: " . CFG_SSO_ENDPOINT_URL . '?' . ModuleSSO::LOGOUT_KEY . '=1&' . ModuleSSO::CONTINUE_KEY . '=' . CFG_DOMAIN_URL);
        }
    }
    
    public function run()
    {
        $this->login();
        $this->logout();
    }
    
    public function showHTMLLoginFailed()
    {
        return "<div class='message warn'>Login failed, please try again</div>";
    }
    
    public function appendScripts()
    {
        echo $this->loginMethod->appendScripts();
    }
    
    public function showLoginMethodHTML() {
        echo $this->loginMethod->clientHTML($this->getContinueUrl());
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
            if($method == NoScriptLogin::METHOD_NUMBER) {
                $this->loginMethod = new NoScriptLogin();
            } else if($method == IframeLogin::METHOD_NUMBER) {
                $this->loginMethod = new IframeLogin();
            } else if($method == CORSLogin::METHOD_NUMBER) {
                $this->loginMethod = new CORSLogin();
            } else {
                $this->loginMethod = new DirectLogin();
            }
        } else {
            $this->loginMethod = new DirectLogin();
        }
        $this->loginMethod->run();
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
    
    public function __construct($issuer = CFG_JWT_ISSUER, $expiration = null, $issuedAt = null, $notBefore = null, $privKeyPath = null)
    {
        $this->privateKey = $privKeyPath ? file_get_contents($privKeyPath) : file_get_contents(__DIR__ . '/../config/pk.pem');
        $this->issuer = $issuer;
       
        $this->expiration = $expiration ? $expiration : time() + 3600;
        $this->issuedAt = $issuedAt ? $issuedAt : time();
        $this->notBefore = $notBefore ? $notBefore : time();
    }
    
    /**
     * Generates and returns JWT based on input values and config variables
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
        
        $token = $builder->sign($signer, $keychain->getPrivateKey($this->privateKey))
                ->getToken();

        $this->token = $token;
        return $token;
    }
}

interface ILoginMethod
{
    /*
     * Takes care of login process
     */
    public function login();
}

abstract class LoginMethod implements ILoginMethod
{   
    public $domain = '';
    public $continueUrl = '';
    
    /*
     * Sets and updates SSO cookies
     */
    public function setCookies($userId)
    { 
        $time = time();
        $ssoCookie = Cookie::generateHash($userId);
        setcookie(Cookie::SSOC, $ssoCookie, null, null, null, null, true);
        
        $query = Database::$pdo->prepare("UPDATE users SET cookie = '$ssoCookie',logged = '$time' WHERE id = $userId");
        $query->execute();
        
    }
    
    /*
     * Unsets SSO cookies
     */
    public function unsetCookies()
    {
        setcookie(Cookie::SSOC, null, -1, '/');
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
        $this->logout();
        $this->login();
    }
    
        
    public function getContinueUrl()
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
    
    public function appendScripts()
    {
        
    }
    
    public function logout()
    {
        if(isset($_GET[ModuleSSO::LOGOUT_KEY]) && $_GET[ModuleSSO::LOGOUT_KEY] == 1) {
            session_destroy();
            $this->unsetCookies();
            $this->redirect($this->getContinueUrl());
        }
    }
    
}

abstract class ClassicLogin extends LoginMethod
{
    public function login()
    {
        $this->continueUrl = $this->getContinueUrl();
        if(isset($_GET['email']) && isset($_GET['password'])) {
            $email =  $_GET['email'];
            $password =  $_GET['password'];

            $query = Database::$pdo->prepare("SELECT * FROM users WHERE email = ? AND password = ?");
            $query->execute(array($email, $password));
            $user = $query->fetch();
            if($user) {
                $this->setCookies($user['id']);
                $token = (new JWT())->generate(array('uid' => $user['id'], 'continue' => $this->continueUrl));

                $url = $this->continueUrl .  "?" . ModuleSSO::TOKEN_KEY . "=" . $token;
                $this->redirect($url);
            } else {
                echo $this->showHTMLLoginForm();
            }
        } else if(isset($_GET['login'])) {
            if(isset($_COOKIE[Cookie::SSOC])) {
                $user = $this->getUserFromCookie();
                if($user) {
                    $token = (new JWT())->generate(array('uid' => $user['id'], 'continue' => $this->continueUrl));
                    $url = $this->continueUrl .  "?" . ModuleSSO::TOKEN_KEY . "=" . $token;
                    $this->redirect($url);
                } else {
                    echo $this->showHTMLLoginForm();
                }
            } else {
                echo $this->showHTMLLoginForm();
            }
        }
        else if (isset($_GET[ModuleSSO::RELOG_KEY])){
            echo $this->showHTMLLoginForm();
        }
        else if(isset($_GET[ModuleSSO::LOGOUT_KEY])) {
            echo $this->showHTMLLoginForm();
        } else {
            $this->showHTML();
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
                <input type="hidden" name="' . ModuleSSO::METHOD_KEY . '" value="' . static::METHOD_NUMBER . '"/>
                <input type="submit" value="Login with SSO"/>
           </form>';
        
    }
    
    public function showHTMLUserInfo($user)
    {
        $html = '<div><p>You are logged in as <strong>' . $user['email'] . '</strong> at <a href="' . CFG_SSO_ENDPOINT_URL . '">Webgarden SSO</a></p><ul>';
        if ($this->continueUrl !== CFG_SSO_ENDPOINT_URL) {
            $data = array(
                ModuleSSO::METHOD_KEY => static::METHOD_NUMBER,
                ModuleSSO::LOGIN_KEY => 1,
                ModuleSSO::CONTINUE_KEY => $this->continueUrl
                );
            $query = http_build_query($data);
            $src = CFG_SSO_ENDPOINT_URL . '?' . $query;
            $html .= '<li><a href="' . $src . '" title="Continue as ' . $user['email'] . '"> Continue as ' . $user['email'] . '</a></li>';
        }
        $data = array(
                ModuleSSO::METHOD_KEY => static::METHOD_NUMBER,
                ModuleSSO::RELOG_KEY => 1,
                ModuleSSO::CONTINUE_KEY => $this->continueUrl
                );
        $query = http_build_query($data);
        $src = CFG_SSO_ENDPOINT_URL . '?' . $query;
        $html .= '<li><a href="' . $src . '" title="Log in as another user">Log in as another user</a></ul></div>';
        return $html; 
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
}

class NoScriptLogin extends ClassicLogin
{   
    const METHOD_NUMBER = 1;
    public function clientHTML($continue)
    {
        return '<form method="get" action="'. CFG_SSO_ENDPOINT_URL .'">
                <input type="hidden" name="' . ModuleSSO::CONTINUE_KEY . '" value="' . $continue . '"/>
                <input type="hidden" name="' . ModuleSSO::METHOD_KEY . '" value="' . self::METHOD_NUMBER . '"/>
                <input type="submit" value="Login with SSO"/>
            </form>';
        
    }
    
    public function redirect($url, $code = 302)
    {
        http_response_code($code);
        header("Location: " . $url);
        exit;
    }
}

class IframeLogin extends ClassicLogin
{   
    const METHOD_NUMBER = 2;
    public function clientHTML($continue)
    {
        $data = array(
                ModuleSSO::METHOD_KEY => self::METHOD_NUMBER,
                ModuleSSO::CONTINUE_KEY => $continue
                );
        $query = http_build_query($data);
        $src = CFG_SSO_ENDPOINT_URL . '?' . $query;
        return "<div><iframe src='$src' width='100%' frameborder='0'></iframe></div>";
    }
    
    public function redirect($url)
    {
        echo "<script>window.parent.location = '" . $url . "';</script>";
    }
}

class CORSLogin extends LoginMethod
{
    const METHOD_NUMBER = 3;
    public function clientHTML()
    {
        $str = '<div id="id-login-area">';
        $str .= '<form id="id-sso-form" action="' . CFG_SSO_ENDPOINT_URL . '">'
                . '<label>'
                    . 'Email: <input type="text" name="email"/>'
                . '</label>'
                . '<br/>'
                . '<label>'
                    . 'Password: <input type="password" name="password"/>'
                . '</label>'
                . '<br/>'
                . '<input type="submit" id="id-login-button" value="Login with SSO"/>'
            . '</form>';
        $str .= '</div>';
        return $str;
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
                echo json_encode(array("status" => "user_not_found"));
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
    
    public function appendScripts()
    {
        return "<script src='http://sso.local/app/module_sso/prototype.js'></script>
        <script src='http://sso.local/app/module_sso/cors.js'></script>";
        
    }
}

class DirectLogin extends ClassicLogin
{
    public function redirect($url = CFG_SSO_ENDPOINT_URL, $code = 302)
    {
        http_response_code($code);
        header("Location: " . $url);
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
        $html = '<div><p>You are logged in as <strong>' . $user['email'] . '</strong> at Webgarden SSO</p><ul>';
        $html .= '<li><a href="' . CFG_SSO_ENDPOINT_URL . '?' . ModuleSSO::RELOG_KEY . '=1" title="Log in as another user">Log in as another user to Webgarden SSO</a>';
        $html .= '<li><a href="?' . ModuleSSO::LOGOUT_KEY. '=1" title="Logout">Logout from Webgarden SSO</a>';
        $html .= '</ul></div>';
        return $html;
    }
}