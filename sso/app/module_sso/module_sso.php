<?php
session_start();
require_once __DIR__ .'/../config/config.inc.php';
require_once 'browserSniffer.php';

use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Keychain;
use Lcobucci\JWT\Signer\Rsa\Sha256;

class ModuleSSO
{
    /**
     * @var LoginMethod $loginMethod
     */
    public $loginMethod = null;
    
    
    //TODO will be client method
    public function pickLoginMethod()
    {
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
                // TODO JS ENABLED
                $browser = new BrowserSniffer();
                if(isset($supportedBrowsers[$browser->getName()])) {
                    if($browser->getVersion() >= $supportedBrowsers[$browser->getName()]) {
                        $this->loginMethod = new CORSLogin();
                        break;
                    }
                }   
            }
            else if($method === 'iframe') {
                // TODO JS ENABLED
                $this->loginMethod = new IframeLogin();
                break;
            }
            else if($method === 'noscript') {
                 $this->loginMethod = new NoScriptLogin();
                 break;
            }
        } 
    }
    
    public function run()
    {
        if(isset($_GET['m'])) {
            $method = $_GET['m'];
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
    const CONTINUE_URL_KEY = 'continue';
    private $whiteList = array();
    public function __construct()
    {        
        //TODO load from config or DB
        $this->whiteList = array('domain1.local', 'domain2.local', 'sso.local');
    }
    
    public function getUrl()
    {
        if(isset($_GET[self::CONTINUE_URL_KEY])) {
            $url = $_GET[self::CONTINUE_URL_KEY];
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
    
    public function isInWhitelist($domain)
    {
        if(in_array($domain, $this->whiteList)) {
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
    public function setCookies($userId)
    {
        //user ID and last login time cookie
        $time = time();
        $sLastLoggedTime = hash('sha256', $time);
        $sID = hash('sha256', $userId);
        setcookie('SID', $sID);
        setcookie('SLLT', $sLastLoggedTime);
        $query = Database::$pdo->prepare("UPDATE users SET cookie = '$sID',logged = '$sLastLoggedTime' WHERE id = $userId");
        $query->execute();
        
    }
    
    public function getUserFromCookie()
    {
        if(isset($_COOKIE['SID']) && isset($_COOKIE['SLLT'])) {
            $cookie = $_COOKIE['SID'];
            $logged = $_COOKIE['SLLT'];
            $query = Database::$pdo->prepare("SELECT * FROM users WHERE logged = '$logged' AND cookie = '$cookie'");
            $query->execute();
            $user = $query->fetch();
            if($user) {
                return $user;
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
    public $continueUrl = null;
    
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
                <input type="hidden" name="continue" value="' . $this->continueUrl .  '"/>
                <input type="hidden" name="m" value="1"/>
                <input type="submit" value="Login"/>
           </form>';
        
    }
    
    public function showHTMLUserInfo($user)
    {
        $html = '<div>
               <p>You are logged in as <strong>' . $user['email'] . '</strong></p>
               <ul>';
               if ($this->continueUrl !== CFG_SSO_ENDPOINT_URL) {
                   $html .= '<li><a href="' . CFG_SSO_ENDPOINT_URL . '?m=1&login=1&continue=' . $this->continueUrl . '" title="Continue as ' . $user['email'] . '"> Continue as ' . $user['email'] . '</a></li>';
               }
               $html .= '<li><a href="' . CFG_SSO_ENDPOINT_URL . '?m=1&relog=1&continue=' . $this->continueUrl . '" title="Log in as another user">Log in as another user</a>
               </ul>
           </div>';
        return $html;
        
    }
    
    public function login()
    {
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

                $url = $this->continueUrl .  "?token=" . $token;
                Logger::log($this->continueUrl);
                $this->redirect($url);
            } else {
                echo $this->showHTMLLoginForm();
            }
        } else if(isset($_GET['login'])) {
            if(isset($_COOKIE['SID']) && isset($_COOKIE['SLLT'])) {
                $user = $this->getUserFromCookie();
                if($user) {
                    $token = (new JWT())->generate(array('uid' => $user['id']));
                    $url = $this->continueUrl .  "?token=" . $token;
                    Logger::log($this->continueUrl);
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
                <input type="hidden" name="continue" value="' . $this->continueUrl .  '"/>
                <input type="hidden" name="m" value="2"/>
                <input type="submit" value="Login"/>
           </form>';
        
    }
    
    public function showHTMLUserInfo($user)
    {
        $html = '<div>
               <p>You are logged in as <strong>' . $user['email'] . '</strong></p>
               <ul>';
               if ($this->continueUrl !== CFG_SSO_ENDPOINT_URL) {
                   $html .= '<li><a href="' . CFG_SSO_ENDPOINT_URL . '?m=2&login=1&continue=' . $this->continueUrl . '" title="Continue as ' . $user['email'] . '"> Continue as ' . $user['email'] . '</a></li>';
               }
               $html .= '<li><a href="' . CFG_SSO_ENDPOINT_URL . '?m=2&relog=1&continue=' . $this->continueUrl . '" title="Log in as another user">Log in as another user</a>
               </ul>
           </div>';
        return $html;
        
    }
    
    public function login()
    {
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

                $url = $this->continueUrl .  "?token=" . $token;
                Logger::log($this->continueUrl);
                $this->redirect($url);
            } else {
                echo $this->showHTMLLoginForm();
            }
        } else if(isset($_GET['login'])) {
            if(isset($_COOKIE['SID']) && isset($_COOKIE['SLLT'])) {
                $user = $this->getUserFromCookie();
                if($user) {
                    $token = (new JWT())->generate(array('uid' => $user['id']));
                    $url = $this->continueUrl .  "?token=" . $token;
                    Logger::log($this->continueUrl);
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
    public function checkCookie()
    {
        header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
        header('Access-Control-Allow-Credentials: true');
        header('Content-Type: application/json');
        if(!isset($_COOKIE['SID']) || !isset($_COOKIE['SLLT'])) {
            echo json_encode(array("status" => "no_cookie"));
        } else {
            $sid = $_COOKIE['SID'];
            $sllt = $_COOKIE['SLLT'];
            $query = Database::$pdo->prepare("SELECT * FROM users WHERE logged = '$sllt' AND cookie = '$sid'");
            $query->execute(array());
            $user = $query->fetch();
            if($user) {
                $token = (new JWT())->generate(array('uid' => $user['id']));
                echo '{"status": "ok", "token": "' . $token . '", "email": "' . $user['email'] .'"}';
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

                echo '{"status": "ok", "token": "' . $token . '"}';
            } else {
                echo json_encode(array("status" => "fail"));
            }
        } else {
            echo json_encode(array("status" => "bad_login"));
        }

        
    }
    
    public function run()
    {
        global $whiteList;
        if(isset($_SERVER['HTTP_ORIGIN']) && in_array($_SERVER['HTTP_ORIGIN'], $whiteList)){
            if(isset($_GET['login']) && $_GET['login'] == 1) {
                $this->login();
            } else if(isset($_GET['checkCookie']) && $_GET['checkCookie'] == 1) {
                $this->checkCookie();
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

$module_sso = new ModuleSSO();
$module_sso->run();