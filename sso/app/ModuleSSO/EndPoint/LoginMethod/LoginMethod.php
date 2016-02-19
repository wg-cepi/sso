<?php
namespace ModuleSSO\EndPoint;

use ModuleSSO\Cookie;
use ModuleSSO\JWT;

/**
 * Class LoginMethod
 *
 * @package ModuleSSO\EndPoint
 */
abstract class LoginMethod implements ILoginMethod
{
    /**
     * Domain where the login request started
     * @var string $domain
     */
    public $domain = CFG_JWT_ISSUER;

    /**
     * URL where user should continue after login
     * @var string $continueUrl
     */
    public $continueUrl = CFG_SSO_ENDPOINT_URL;


    /**
     * Returns domain
     * @return string
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * Returns continueUrl
     * @return string
     */
    public function getContinueUrl()
    {
        return $this->continueUrl;
    }

    /**
     * Returns number of login method
     *
     * @return int Number of login method
     */
    public function getMethodNumber()
    {
        return static::METHOD_NUMBER;
    }

    /**
     * Redirects user to given URL
     * If no URL is given, redirects to default SSO endpoint URL
     *
     * @param string $url URL where user will be redirected
     * @param int $code HTTP header code for temporary redirect
     * @return mixed
     */
    public function redirect($url = CFG_SSO_ENDPOINT_URL, $code = 302)
    {
        http_response_code($code);
        header("Location: " . $url);
        exit;
    }


    /**
     * Method for appending JavaScript scripts to HTML
     *
     * @return string
     */
    public function appendScripts()
    {
        return '';
    }

    /**
     * Method for appending CSS styles to HTML
     *
     * @return string
     */
    public function appendStyles()
    {
        return '<link rel="stylesheet" href="css/styles.css">';
    }

    /**
     * {@inheritdoc}
     *
     * Waits for specific $_GET parameter and performs logout action by destroying session.
     * After that redirects user back to where he came from.
     */
    public function logoutListener()
    {
        if(isset($_GET[\ModuleSSO::LOGOUT_KEY]) && $_GET[\ModuleSSO::LOGOUT_KEY] == 1) {
            session_destroy();
            $this->unsetSSOCookie();
            $this->redirect($this->getContinueUrl());
        }
    }

    /**
     * Method tries to obtain a user from database by identified stored in SSO cookie
     *
     * @return mixed|null Returns either user or null
     *
     * @uses LoginMethod::setOrUpdateSSOCookie()
     */
    public function getUserFromCookie()
    {
        $result = null;
        if(isset($_COOKIE[Cookie::SECURE_SSO_COOKIE])) {
            $toBeListed = explode(":", $_COOKIE[Cookie::SECURE_SSO_COOKIE]);
            if(count($toBeListed) === 2) {
                list($identifier, $token) = $toBeListed;
                $query = \Database::$pdo->prepare("SELECT * FROM users WHERE cookie = '$identifier:$token'");
                $query->execute();
                $user = $query->fetch();
                if($user) {
                    $this->setOrUpdateSSOCookie($user['id']);
                    return $user;
                }
            }
        }
        return $result;
    }

    /**
     * Obtains continue parameter from $_GET, $_SESSION or $_SERVER['HTTP_REFERER']
     * Continue parameter is validated and $continueUrl and  $domain are set
     *
     * @uses LoginMethod::isInWhiteList()
     * @uses LoginMethod::setContinueUrl()
     */
    public function continueUrlListener()
    {
        $returnUrl = $url = CFG_SSO_ENDPOINT_URL;
        if(isset($_GET[\ModuleSSO::CONTINUE_KEY])) {
            $url = $_GET[\ModuleSSO::CONTINUE_KEY];
        } else if(isset($_SESSION[\ModuleSSO::CONTINUE_KEY])) {
            $url = $_SESSION[\ModuleSSO::CONTINUE_KEY];
        } else if(isset($_SERVER['HTTP_REFERER'])) {
            $url = $_SERVER['HTTP_REFERER'];
        }

        $parsed = parse_url($url);
        if(!empty($parsed['host']) && !empty($parsed['scheme'])) {
            if ($this->isInWhiteList($parsed['host'])) {
                $returnUrl = $parsed['scheme'] . '://' . $parsed['host'];
                if(!empty($parsed['path'])) {
                    $returnUrl .= $parsed['path'];
                }
            }
        }
        $this->setContinueUrl($returnUrl);

        //clear session
        unset($_SESSION[\ModuleSSO::CONTINUE_KEY]);
    }

    /**
     * Starts lifecycle of LoginMethod
     *
     * @uses LoginMethod::continueUrlListener()
     * @uses LoginMethod::loginListener()
     * @uses LoginMethod::logoutListener()
     */
    public function perform()
    {
        $this->continueUrlListener();
        $this->loginListener();
        $this->logoutListener();
    }

    /**
     * Hashes password by user
     *
     * @param string $password Plain password
     * @return string Hashed password
     */
    protected function generatePasswordHash($password)
    {
        //automatic salt
        return crypt($password);
    }

    /**
     * Compares password gived by user and stored hashed password
     *
     * @link http://us.php.net/manual/en/function.hash-equals.php#118384
     *
     * @param string $password Password provided by user
     * @param string $hashedPassword Hashed password
     *
     * @return bool
     */
    protected function verifyPasswordHash($password, $hashedPassword)
    {
        return substr_count($hashedPassword ^ crypt($password, $hashedPassword), "\0") * 2 === strlen($hashedPassword . crypt($password, $hashedPassword));
    }

    /**
     * Sets and updates SSO cookie
     * SSO cookie is updated every time when user accesses login URL
     *
     * @uses Cookie::generateHash()
     *
     * @param int $userId
     */
    protected function setOrUpdateSSOCookie($userId)
    {
        $identifier = md5(Cookie::SALT . md5(Cookie::generateHash($userId) . Cookie::SALT));
        $token = md5(uniqid(rand(), TRUE));
        $timeout = time() + 60 * 60 * 24 * 7;

        setcookie(Cookie::SECURE_SSO_COOKIE, "$identifier:$token", $timeout, null, null, null, true);

        $query = \Database::$pdo->prepare("UPDATE users SET cookie = '$identifier:$token' WHERE id = $userId");
        $query->execute();
    }

    /**
     * Generates JWT and sends it to the domain
     *
     * @param array $user
     * @throws \Exception
     *
     * @uses JWT::generate()
     */
    protected function generateTokenAndRedirect($user)
    {
        $url = $this->continueUrl;
        if($this->continueUrl !== CFG_SSO_ENDPOINT_URL) {
            $token = (new JWT($this->getDomain()))->generate(array('uid' => $user['id']));
            $url .=  "?" . \ModuleSSO::TOKEN_KEY . "=" . $token;
        }
        $this->redirect($url);

    }

    /**
     * Unsets SSO cookie
     */
    protected function unsetSSOCookie()
    {
        setcookie(Cookie::SECURE_SSO_COOKIE, null, -1, '/');
    }

    /**
     * Checks if domain exists in database. If so, method sets domain property to that value otherwise domain value is untouched
     *
     * @uses LoginMethod::setDomain()
     *
     * @param string $domainName Domain extracted from $continueUrl
     * @return bool
     */
    protected function isInWhiteList($domainName)
    {
        $fullDomainName = $domainName;

        //find root domain
        $exploded = explode(".", $domainName);
        $tld = array_pop($exploded);
        $main = array_pop($exploded);
        $domainName = $main . "." . $tld;

        //check if full domain matches
        $query = \Database::$pdo->prepare("SELECT * FROM domains WHERE name= '$fullDomainName'");
        $query->execute();
        $domain = $query->fetch();
        if($domain) {
            $this->setDomain($domain['name']);
            return true;
        } else {
            if($domainName !== $fullDomainName) {
                //check if root domain matches
                $query = \Database::$pdo->prepare("SELECT * FROM domains WHERE name= '$domainName'");
                $query->execute();
                $domain = $query->fetch();
                if($domain) {
                    $this->setDomain($domain['name']);
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Sets domain
     * @param string $domain
     */
    private function setDomain($domain)
    {
        $this->domain = $domain;
    }

    /**
     * Sets continue URL
     * @param string $url
     */
    private function setContinueUrl($url)
    {
        $this->continueUrl = $url;
    }


}
