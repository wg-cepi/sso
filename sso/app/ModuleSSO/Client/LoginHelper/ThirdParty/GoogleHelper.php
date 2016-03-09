<?php
namespace ModuleSSO\Client\LoginHelper\ThirdParty;

class GoogleHelper extends ThirdPartyHelper
{
    public function showLogin($continue = '')
    {
        $this->renderer->renderLogin();
    }
}
