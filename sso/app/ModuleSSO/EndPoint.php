<?php
namespace ModuleSSO;

use ModuleSSO\LoginMethod\ClassicLogin\NoScriptLogin;
use ModuleSSO\LoginMethod\ClassicLogin\IframeLogin;
use ModuleSSO\LoginMethod\ClassicLogin\DirectLogin;
use ModuleSSO\LoginMethod\CORSLogin;

use ModuleSSO\LoginMethod\ThirdPartyLogin\FacebookLogin;

class EndPoint extends \ModuleSSO
{
    /**
     * @var LoginMethod $loginMethod
     */
    public $loginMethod = null;
    
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
            } else {
                $this->loginMethod = new DirectLogin();
            }
        } else {
            $this->loginMethod = new DirectLogin();
        }
    }
    
    public function appendStyles()
    {
        return $this->loginMethod->appendStyles();
    }
    
    public function run()
    {
        $this->loginMethod->run();
    }
}