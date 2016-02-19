<?php
namespace ModuleSSO;

use ModuleSSO\EndPoint\LoginMethod;
use ModuleSSO\EndPoint\LoginMethod\HTTP;
use ModuleSSO\EndPoint\LoginMethod\Other;
use ModuleSSO\EndPoint\LoginMethod\ThirdParty;

/**
 * Class EndPoint
 * @package ModuleSSO
 */
class EndPoint extends \ModuleSSO
{
    /**
     * @var \ModuleSSO\EndPoint\LoginMethod $loginMethod
     */
    private $loginMethod = null;

    /**
     * @var array $MAP
     */
    private static $MAP = array(
        HTTP\NoScriptLogin::METHOD_NUMBER => '\ModuleSSO\EndPoint\LoginMethod\HTTP\NoScriptLogin',
        HTTP\IframeLogin::METHOD_NUMBER => '\ModuleSSO\EndPoint\LoginMethod\HTTP\IframeLogin',
        Other\CORSLogin::METHOD_NUMBER => '\ModuleSSO\EndPoint\LoginMethod\Other\CORSLogin',
        ThirdParty\FacebookLogin::METHOD_NUMBER => '\ModuleSSO\EndPoint\LoginMethod\ThirdParty\FacebookLogin',
        ThirdParty\GoogleLogin::METHOD_NUMBER => '\ModuleSSO\EndPoint\LoginMethod\ThirdParty\GoogleLogin'
    );

    /**
     * Sets $loginMethod according to parameter passed in $_GET
     * If there is no parameter, EndPoint::$loginMethod is set to DirectLogin
     *
     * @uses EndPoint::$loginMethod
     * @uses $_GET
     * @uses \ModuleSSO
     * @uses NoScriptLogin
     * @uses IframeLogin
     * @uses CORSLogin
     * @uses FacebookLogin
     * @uses GoogleLogin
     * @uses DirectLogin
     */
    public function pickLoginMethod()
    {
        if(isset($_GET[\ModuleSSO::METHOD_KEY])) {
            $key = $_GET[\ModuleSSO::METHOD_KEY];
            if(isset(self::$MAP[$key])) {
                $class = self::$MAP[$key];
                $this->loginMethod = new $class();
            } else {
                $this->loginMethod = new HTTP\DirectLogin();
            }
        } else {
            $this->loginMethod = new HTTP\DirectLogin();
        }
    }

    /**
     * Returns link to CSS file depending on type of EndPoint::$loginMethod
     *
     * @uses \ModuleSSO\EndPoint\LoginMethod
     * @return string
     */
    public function appendStyles()
    {
        return $this->loginMethod->appendStyles();
    }

    /**
     * Starts lifecycle of EndPoint
     * @uses \ModuleSSO\EndPoint\LoginMethod::perform()
     */
    public function run()
    {
        $this->loginMethod->perform();
    }

    /**
     * Returns login method
     * @return LoginMethod
     */
    public function getLoginMethod()
    {
        return $this->loginMethod;
    }

    /**
     * Sets login method
     * @param LoginMethod $loginMethod
     */
    public function setLoginMethod(LoginMethod $loginMethod)
    {
        $this->loginMethod = $loginMethod;
    }
}