<?php
namespace ModuleSSO;

use ModuleSSO\LoginMethod\ClassicLogin\NoScriptLogin;
use ModuleSSO\LoginMethod\ClassicLogin\IframeLogin;
use ModuleSSO\LoginMethod\ClassicLogin\DirectLogin;

use ModuleSSO\LoginMethod\CORSLogin;

use ModuleSSO\LoginMethod\ThirdPartyLogin\FacebookLogin;
use ModuleSSO\LoginMethod\ThirdPartyLogin\GoogleLogin;

/**
 * Class EndPoint
 * @package ModuleSSO
 */
class EndPoint extends \ModuleSSO
{
    /**
     * @var LoginMethod $loginMethod
     */
    public $loginMethod = null;

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
            if($m == NoScriptLogin::METHOD_NUMBER) {
                $this->loginMethod = new NoScriptLogin();
            } else if($m == IframeLogin::METHOD_NUMBER) {
                $this->loginMethod = new IframeLogin();
            } else if($m == CORSLogin::METHOD_NUMBER) {
                $this->loginMethod = new CORSLogin();
            } else if($m == FacebookLogin::METHOD_NUMBER) {
                $this->loginMethod = new FacebookLogin();
            } else if($m == GoogleLogin::METHOD_NUMBER) {
                $this->loginMethod = new GoogleLogin();
            } else {
                $this->loginMethod = new DirectLogin();
            }
        } else {
            $this->loginMethod = new DirectLogin();
        }
    }

    /**
     * Returns link to CSS file depending on type of @see LoginMethod
     * @return string
     */
    public function appendStyles()
    {
        return $this->loginMethod->appendStyles();
    }

    /**
     * @see LoginMethod::run()
     */
    public function run()
    {
        $this->loginMethod->run();
    }
}