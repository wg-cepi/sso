<?php
namespace ModuleSSO\Client\LoginHelper\HTTP;

class NoScriptHelper extends HTTPHelper
{
    public function showLogin($continue = '')
    {
        $this->renderer->renderLogin(array('continueUrl' => $continue));
    }
}