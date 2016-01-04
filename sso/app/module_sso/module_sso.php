<?php
session_start();
require_once __DIR__ .'/../config/config.inc.php';
require_once 'browserSniffer.php';

class ModuleSSO
{
    /**
     * @var LoginMethod $loginMethod
     */
    public $loginMethod = null;
    
    
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
                /*
                // TODO JS ENABLED
                $browser = new BrowserSniffer();
                if(isset($supportedBrowsers[$browser->getName()])) {
                    if($browser->getVersion() >= $supportedBrowsers[$browser->getName()]) {
                        $this->loginMethod = new CORSLogin();
                        break;
                    }
                }
                 */
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
        
        //$this->loginMethod = new NoScriptLogin();
        
    }
}

use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Keychain;
use Lcobucci\JWT\Signer\Rsa\Sha256;

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

class EHostNotInWhitelist extends \Exception {}
class EHostNotFoundInURL extends \Exception {}
class EEmptyURL extends \Exception {}
class ContinueUrl
{
    private $url = "";
    private $whiteList = array();
    public function __construct($url)
    {        
        $this->url = $url;
        //TODO load from config or DB
        $this->whiteList = array('domain1.local', 'domain2.local', 'sso.local');
    }
    
    public function getUrl()
    {
        if($this->url !== "") {
            $parsed = parse_url($this->url);
            if(!empty($parsed['host'])) {
                if(in_array($parsed['host'], $this->whiteList)) {
                    return $this->url;
                } else {
                    throw new EHostNotInWhitelist("Host in continue URL is not in whitelist");
                }
            } else {
                throw new EHostNotFoundInURL("Host not found in continue URL");
            }
        } else {
            throw new EEmptyURL("Continue URL empty");
        }
    }
    
    public function getLikelyUrl()
    {
        
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
        //TODO signed
        //TODO verify them
        $time = time();
        setcookie('SID', $userId);
        setcookie('SLLT', $time);
        $query = Database::$pdo->prepare("UPDATE users SET cookie = '$userId',logged = '$time' WHERE id = $userId");
        $query->execute();
        
    }
    
    public function getUserFromCookie()
    {
        //TODO verify SID and SLLT cookie
        if(isset($_COOKIE['SID']) && isset($_COOKIE['SID'])) {
            $cookie = $_COOKIE['SID'];
            $logged = $_COOKIE['SLLT'];
            $query = Database::$pdo->prepare("SELECT * FROM users WHERE logged = $logged AND cookie = '$cookie'");
            $query->execute();
            $user = $query->fetch();
            if($user) {
                return $user;
            } else {
                throw new \Exception("User not found");
            }
        } else {
            throw new \Exception("Cookies not set, possible security breach");
        }
    }
    
    public function showHTMLLoginForm(ContinueUrl $continueUrl)
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
               <input type="hidden" name="continue" value="' . $continueUrl->getUrl() . '"/>
               <input type="submit" value="Login"/>
           </form>';
        
    }
    
    public function showHTMLUserInfo($user, ContinueUrl $continueUrl)
    {
        $html = '<div>
               <p>You are logged in as <strong>' . $user['email'] . '</strong></p>
               <ul>';
               if ($continueUrl->getUrl() !== CFG_SSO_ENDPOINT_URL) {
                   $html .= '<li><a href="' . CFG_SSO_ENDPOINT_URL . '?login=1&continue=' . $continueUrl->getUrl() . '" title="Continue as ' . $user['email'] . '"> Continue as ' . $user['email'] . '</a></li>';
               }
               $html .= '<li><a href="' . CFG_SSO_ENDPOINT_URL . '?relog=1&continue=' . $continueUrl->getUrl() . '" title="Log in as another user">Log in as another user</a>
               </ul>
           </div>';
        return $html;
        
    }
    
    public function showHTML(ContinueUrl $continueUrl)
    {
        $user = null;
        
        try {
            $user = $this->getUserFromCookie();
        } catch (\Exception $e) {
            //user not found or cookies deleted/forged
        }
        if (!isset($_COOKIE['SID']) || !isset($_COOKIE['SLLT']) || isset($_GET['relog']) || $user === null) {
            echo $this->showHTMLLoginForm($continueUrl);
        } else if (isset($_COOKIE['SID']) && isset($_COOKIE['SLLT']) && !isset($_GET['relog']) && $user !== null) {
            echo $this->showHTMLUserInfo($user, $continueUrl);
        }
    }
    
    public function login()
    {
        try {
            if(isset($_GET['continue'])) {
                $continueUrl = new ContinueUrl($_GET['continue']);
            } else {
                $continueUrl = new ContinueUrl(CFG_SSO_ENDPOINT_URL);
            }
        } catch (EHostNotInWhitelist $e) {
            $continueUrl = new ContinueUrl(CFG_SSO_ENDPOINT_URL);
        } catch (EHostNotFoundInURL $e) {
            $continueUrl = new ContinueUrl(CFG_SSO_ENDPOINT_URL);
        } catch (EEmptyURL $e) {
            $continueUrl = new ContinueUrl(CFG_SSO_ENDPOINT_URL);;
        }
        if(isset($_GET['email']) && isset($_GET['password'])) {
            $email =  $_GET['email'];
            $password =  $_GET['password'];

            $query = Database::$pdo->prepare("SELECT * FROM users WHERE email = ? AND password = ?");
            $query->execute(array($email, $password));
            $user = $query->fetch();
            if($user) {
                $this->setCookies($user['id']);

                $jwt = new JWT();
                $token = $jwt->generate(array('uid' => $user['id']));

                $url = $continueUrl->getUrl() .  "?token=" . $token;
                $this->redirect($url);
            } else {
                $this->showHTML($continueUrl);
            }
        } else if(isset($_COOKIE['SID']) && isset($_COOKIE['SID']) && isset($_GET['login'])) {
            $user = null;
            try {
                $user = $this->getUserFromCookie();
            } catch (\Exception $e) {
                //user not found or cookies deleted/forged
            }
            if($user !== null) {
                $jwt = new JWT();
                $token = $jwt->generate(array('uid' => $user['id']));

                $url = $continueUrl->getUrl() . "?token=" . $token;
                $this->redirect($url);
            } else {
                $this->showHTML($continueUrl);
            }
        }
        else {
            $this->showHTML($continueUrl);
        }
    }
    
}

class NoScriptLogin extends LoginMethod
{
    public function redirect($url, $code = 302)
    {
        http_response_code($code);
        header("Location: " . $url);
        exit;
    }
}

class IframeLogin extends LoginMethod
{   
    public function redirect($url)
    {
        echo "<script>window.parent.location = '" . $url . "';</script>";
    }
}
//$rdl = new NoScriptLogin();
//$rdl->login('joe@example.com', 'joe', 'http://sso.local/joe.html');

$module_sso = new ModuleSSO();
$module_sso->pickLoginMethod();
//$module_sso->loginMethod = new IframeLogin();

$module_sso->loginMethod->login();

