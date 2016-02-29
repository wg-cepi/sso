<?php
namespace ModuleSSO;

use Symfony\Component\HttpFoundation\Request;
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
     * @var Request $request
     */
    public $request = null;

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
     * EndPoint constructor
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

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
        if($key = $this->request->query->get(\ModuleSSO::METHOD_KEY)) {
            if(isset(self::$MAP[$key])) {
                $class = self::$MAP[$key];
                $this->loginMethod = new $class($this->request);
            } else {
                $this->loginMethod = new HTTP\DirectLogin($this->request);
            }
        } else {
            $this->loginMethod = new HTTP\DirectLogin($this->request);
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