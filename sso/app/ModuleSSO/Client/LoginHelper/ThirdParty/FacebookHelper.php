<?php
namespace ModuleSSO\Client\LoginHelper\ThirdParty;

class FacebookHelper extends ThirdPartyHelper
{
    public function showLogin($continue = '')
    {
       $this->renderer->renderLogin();
    }
}
