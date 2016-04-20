<?php
namespace ModuleSSO\Client;

interface ILoginHelper
{
    /**
     * Shows login HTML login form
     *
     * @param string $continue URL where user should continue in login process
     * @return mixed
     *
     * @uses ModuleSSO\Messages::showMessages()
     */
    public function showLogin($continue = '');
}