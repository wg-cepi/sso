<?php
use ModuleSSO\Client\LoginHelper\Renderer\HelperRenderException;

use ModuleSSO\Client\LoginHelper\Renderer\HTML\IframeHelperRenderer;
use ModuleSSO\Client\LoginHelper\Renderer\HTML\NoScriptHelperRenderer;
use ModuleSSO\Client\LoginHelper\Renderer\HTML\CORSHelperRenderer;
use ModuleSSO\Client\LoginHelper\Renderer\HTML\FacebookHelperRenderer;
use ModuleSSO\Client\LoginHelper\Renderer\HTML\GoogleHelperRenderer;

use ModuleSSO\EndPoint\LoginMethod\HTTP\NoScriptLogin;
use ModuleSSO\EndPoint\LoginMethod\HTTP\IframeLogin;
use ModuleSSO\EndPoint\LoginMethod\ThirdParty\FacebookLogin;
use ModuleSSO\EndPoint\LoginMethod\ThirdParty\GoogleLogin;



if($this instanceof NoScriptHelperRenderer) {
    if(!isset($params['continueUrl'])) {
        throw new HelperRenderException("Param continueUrl not set");
    }

    $html .=  '<div id="id-login-area">';
        $href = CFG_SSO_ENDPOINT_URL . '?' . \ModuleSSO::CONTINUE_KEY . '=' . $params['continueUrl'] . '&' . \ModuleSSO::METHOD_KEY . '=' . NoScriptLogin::METHOD_NUMBER;
        $html .= '<a href="' . $href . '" class="button-full mdl-button mdl-js-button mdl-button--raised mdl-button--colored">Login with SSO</a>';
    $html .= '</div>';

} else if($this instanceof IframeHelperRenderer) {
    if(!isset($params['continueUrl'])) {
        throw new HelperRenderException("Param continueUrl not set");
    }
    $data = array(
        \ModuleSSO::METHOD_KEY => IframeLogin::METHOD_NUMBER,
        \ModuleSSO::CONTINUE_KEY => $params['continueUrl']
    );
    $query = http_build_query($data);
    $src = CFG_SSO_ENDPOINT_URL . '?' . $query;

    $html .= '<div>';
        $html .= '<iframe id="id-iframe-login" src="' . $src . '" scrolling="no" frameborder="0"></iframe>';
    $html .= '</div>';
} else if($this instanceof CORSHelperRenderer) {
    include __DIR__ . '/../../../../../EndPoint/LoginMethod/Renderer/HTML/Template/CommonLoginFormTemplate.php';
} else if($this instanceof FacebookHelperRenderer) {
    $src = CFG_SSO_ENDPOINT_URL . '?' . \ModuleSSO::CONTINUE_KEY . '=' . CFG_DOMAIN_URL . '&' . \ModuleSSO::METHOD_KEY . '=' . FacebookLogin::METHOD_NUMBER;
    $html .= '<a href="' . $src . '"><img src="' . CFG_SSO_URL . '/img/fbLogin.png"/></a>';
} else if($this instanceof GoogleHelperRenderer) {
    $src = CFG_SSO_ENDPOINT_URL . '?' . \ModuleSSO::CONTINUE_KEY . '=' . CFG_DOMAIN_URL . '&' . \ModuleSSO::METHOD_KEY . '=' . GoogleLogin::METHOD_NUMBER;
    $html .=  '<a href="' . $src . '"><img src="' . CFG_SSO_URL . '/img/googleLogin.png"/></a>';
}
