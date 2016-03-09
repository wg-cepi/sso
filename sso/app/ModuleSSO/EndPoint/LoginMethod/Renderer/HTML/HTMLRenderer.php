<?php
namespace ModuleSSO\EndPoint\LoginMethod\Renderer\HTML;

use ModuleSSO\EndPoint\LoginMethod;
use ModuleSSO\EndPoint\LoginMethod\Renderer\IRenderer;

use ModuleSSO\EndPoint\LoginMethod\HTTP\DirectLogin;
use ModuleSSO\EndPoint\LoginMethod\HTTP\NoScriptLogin;
use ModuleSSO\EndPoint\LoginMethod\HTTP\IframeLogin;
use ModuleSSO\EndPoint\LoginMethod\Other\CORSLogin;
use ModuleSSO\EndPoint\LoginMethod\ThirdParty\FacebookLogin;
use ModuleSSO\EndPoint\LoginMethod\ThirdParty\GoogleLogin;



class HTMLRenderer implements IRenderer
{
    public function selectRenderer(LoginMethod $loginMethod)
    {
        if($loginMethod instanceof DirectLogin) {
            return new DirectLoginRenderer();
        } else if($loginMethod instanceof NoScriptLogin) {
            return new NoScriptLoginRenderer();
        } else if($loginMethod instanceof IframeLogin) {
            return new IframeLoginRenderer();
        }  else if($loginMethod instanceof CORSLogin) {
            return new CORSLoginRenderer();
        } else if($loginMethod instanceof FacebookLogin) {
            return new FacebookLoginRenderer();
        } else if($loginMethod instanceof GoogleLogin) {
            return new GoogleLoginRenderer();
        }
    }
    public function renderLoginForm($params = array())
    {
        $html = '';
        include 'Template/CommonLoginFormTemplate.php';
        echo $html;
    }

    public function renderContinueOrRelog($params = array())
    {
        $html = '';
        include 'Template/ContinueOrRelogTemplate.php';
        echo $html;
    }

    public function appendHeader()
    {
        return '<h1>' . CFG_SSO_DISPLAY_NAME . '</h1>';
    }


}