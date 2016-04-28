<?php
use ModuleSSO\Messages;

//EndPoint
use ModuleSSO\EndPoint\LoginMethod\Renderer\HTML\NoScriptLoginRenderer;
use ModuleSSO\EndPoint\LoginMethod\Renderer\HTML\DirectLoginRenderer;
use ModuleSSO\EndPoint\LoginMethod\Renderer\HTML\IframeLoginRenderer;

//Client
use ModuleSSO\Client\LoginHelper\Renderer\HTML\CORSHelperRenderer;

if($this instanceof DirectLoginRenderer
    || $this instanceof NoScriptLoginRenderer) {
    $html .= $this->appendHeader();
}

if ($this instanceof CORSHelperRenderer) {
    $html .= '<div class="sso">';
}
$html .= '<div id="id-login-area" class="mdl-card--border mdl-shadow--2dp">';
$html .= '<span id="id-sso-login-header">Login to Webgarden SSO</span>';
    if($this instanceof NoScriptLoginRenderer || $this instanceof IframeLoginRenderer) {
        if(!isset($params['cssClass'])) throw new \Exception('Param "cssClass" not set');

        $html .= '<form id="id-sso-form" action="' . CFG_SSO_ENDPOINT_URL . '" class="' . $params['cssClass'] . '">';
    } else if ($this instanceof CORSHelperRenderer) {
        $html .= '<form id="id-sso-form" action="' . CFG_SSO_ENDPOINT_URL . '">';
    } else {
        $html .= '<form id="id-sso-form" method="post">';
    }
        $html .= '<div class="inputs">';
            $html .= '<div class="input-email mdl-textfield mdl-js-textfield mdl-textfield--floating-label">';
                $html .= '<input type="email" class="mdl-textfield__input" name="email" id="id-email"/>';
                $html .= '<label for="id-email" class="mdl-textfield__label">';
                    $html .= 'Email';
                $html .= '</label>';
            $html .= '</div>';
            $html .= '<div class="input-pass mdl-textfield mdl-js-textfield mdl-textfield--floating-label">';
                $html .= '<label for="id-pass" class="mdl-textfield__label">';
                    $html .= 'Password';
                $html .= '</label>';
                $html .= '<input type="password" class="mdl-textfield__input" name="password" id="id-pass"/>';
            $html .= '</div>';
        $html .= '</div>';
        if($this instanceof NoScriptLoginRenderer || $this instanceof IframeLoginRenderer) {
            if(!isset($params['continueUrl'])) throw new \Exception('Param "continueUrl" not set');
            if(!isset($params['methodNumber'])) throw new \Exception('Param "methodNumber" not set');
            $html .= '<input type="hidden" name="' . \ModuleSSO::CONTINUE_KEY . '" value="' . $params['continueUrl'] .  '"/>';
            $html .= '<input type="hidden" name="' . \ModuleSSO::METHOD_KEY . '" value="' . $params['methodNumber'] . '"/>';
        }
        $html .= '<div class="button-wrap">';
            $html .= '<input type="submit" class="button-full mdl-button mdl-js-button mdl-button--raised" id="id-login-button" value="Login with SSO"/>';
        $html .= '</div>';
        $html .= Messages::showMessages();
    $html .= '</form>';
$html .= '</div>';
if ($this instanceof CORSHelperRenderer) {
    $html .= '</div>';
}


