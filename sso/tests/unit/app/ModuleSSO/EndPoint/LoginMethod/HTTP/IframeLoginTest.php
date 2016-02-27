<?php

use \ModuleSSO\EndPoint\LoginMethod\HTTP\IframeLogin;
use Symfony\Component\HttpFoundation\Request;

class IframeLoginTest extends PHPUnit_Framework_TestCase
{
    public function testHTMLLoginForm()
    {
        $loginMethod = new IframeLogin(Request::createFromGlobals());
        $this->assertRegExp('/.*<input type="hidden" name="' . \ModuleSSO::METHOD_KEY . '" value="' . IframeLogin::METHOD_NUMBER . '"\/>.*/', $loginMethod->showHTMLLoginForm());

    }
}