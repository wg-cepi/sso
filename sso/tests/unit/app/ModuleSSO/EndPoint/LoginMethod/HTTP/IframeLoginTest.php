<?php

use \ModuleSSO\EndPoint\LoginMethod\HTTP\IframeLogin;
class IframeLoginTest extends PHPUnit_Framework_TestCase
{
    public function testHTMLLoginForm()
    {
        $loginMethod = new IframeLogin();
        $this->assertRegExp('/.*<input type="hidden" name="' . \ModuleSSO::METHOD_KEY . '" value="' . IframeLogin::METHOD_NUMBER . '"\/>.*/', $loginMethod->showHTMLLoginForm());

    }
}