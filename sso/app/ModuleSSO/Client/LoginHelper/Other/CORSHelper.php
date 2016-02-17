<?php
namespace ModuleSSO\Client\LoginHelper\Other;

use ModuleSSO\Client\LoginHelper;
use ModuleSSO\BrowserSniffer;
use ModuleSSO\Messages;

class CORSHelper extends LoginHelper
{
    public function showLogin($continue = '')
    {
        $str = '<div class="sso">';
            $str .= '<div id="id-login-area" class="mdl-card--border mdl-shadow--2dp">';
            $str .= '<span id="id-sso-login-header">Login to Webgarden SSO</span>';
                $str .= '<form id="id-sso-form" action="' . CFG_SSO_ENDPOINT_PLAIN_URL . '">'
                        . '<div class="inputs">'
                                . '<div class="input-email mdl-textfield mdl-js-textfield mdl-textfield--floating-label">'
                                    . '<input type="text" class="mdl-textfield__input" name="email" id="id-email"/>'
                                    . '<label for="id-email" class="mdl-textfield__label">'
                                        . 'Email'
                                    . '</label>'

                                . '</div>'
                                . '<div class="input-pass mdl-textfield mdl-js-textfield mdl-textfield--floating-label">'
                                    . '<label for="id-pass" class="mdl-textfield__label">'
                                        . 'Password'
                                    . '</label>'
                                    . '<input type="password" class="mdl-textfield__input" name="password" id="id-pass"/>'
                                . '</div>'
                        . '</div>'
                        . '<div class="button-wrap">'
                            . '<input type="submit" class="button-full mdl-button mdl-js-button mdl-button--raised" id="id-login-button" value="Login with SSO"/>'
                        .'</div>'
                    . '</form>';
                $str .= Messages::showMessages();
            $str .= '</div>';
        $str .= '</div>';
        return $str;
    }
    
    public function appendScripts()
    {
        return "<script src='http://sso.local/js/prototype.js'></script>
        <script src='http://sso.local/js/cors.js'></script>";
        
    }
    
    public function appendStyles()
    {
        return '<link rel="stylesheet" href="' . CFG_SSO_URL . '/css/styles.css">';
    }
    
    public function isSupported()
    {
        global $CORSBrowsers;
        $browser = new BrowserSniffer();
        if(isset($CORSBrowsers[$browser->getName()])) {
            if($browser->getVersion() >= $CORSBrowsers[$browser->getName()]) {
                return true;
            }
        } else {
            return false;
        }
    }
    
}
