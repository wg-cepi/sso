<?php
use ModuleSSO\EndPoint\LoginMethod\HTTP\NoScriptLogin;
use Symfony\Component\HttpFoundation\Request;

class NoScriptLoginTest extends PHPUnit_Framework_TestCase
{
    public function testHTMLLoginForm()
    {
        $loginMethod = new NoScriptLogin(Request::createFromGlobals());
        $this->assertRegExp('/.*<input type="hidden" name="' . \ModuleSSO::METHOD_KEY . '" value="' . NoScriptLogin::METHOD_NUMBER . '"\/>.*/', $loginMethod->showHTMLLoginForm());
    }

}