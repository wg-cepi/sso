<?php
namespace ModuleSSO\Client\LoginHelper\HTTP;

class IframeHelper extends HTTPHelper
{
    public function showLogin($continue = '')
    {
        $this->renderer->renderLogin(array('continueUrl' => $continue));
    }
    
    public function appendStyles()
    {
        return '<link rel="stylesheet" href="' . CFG_SSO_URL . '/css/iframe.styles.css">';
    }
}