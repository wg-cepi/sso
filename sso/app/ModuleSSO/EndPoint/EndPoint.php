<?php
namespace ModuleSSO;


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
            $m = $_GET[\ModuleSSO::METHOD_KEY];
            if($m == HTTP\NoScriptLogin::METHOD_NUMBER) {
                $this->loginMethod = new HTTP\NoScriptLogin();
            } else if($m == HTTP\IframeLogin::METHOD_NUMBER) {
                $this->loginMethod = new HTTP\IframeLogin();
            } else if($m == Other\CORSLogin::METHOD_NUMBER) {
                $this->loginMethod = new Other\CORSLogin();
            } else if($m == ThirdParty\FacebookLogin::METHOD_NUMBER) {
                $this->loginMethod = new ThirdParty\FacebookLogin();
            } else if($m == ThirdParty\GoogleLogin::METHOD_NUMBER) {
                $this->loginMethod = new ThirdParty\GoogleLogin();
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
     * @see \ModuleSSO\EndPoint\LoginMethod
     * @return string
     */
    public function appendStyles()
    {
        return $this->loginMethod->appendStyles();
    }

    /**
     * @see \ModuleSSO\EndPoint\LoginMethod::perform()
     */
    public function run()
    {
        $this->loginMethod->perform();
    }
}