<?php
use ModuleSSO\EndPoint\LoginMethod\HTTP\NoScriptLogin;

class NoScriptLoginTest extends PHPUnit_Framework_TestCase
{
    public function testHTMLLoginForm()
    {
        $loginMethod = new NoScriptLogin();
        $this->assertRegExp('/.*<input type="hidden" name="' . \ModuleSSO::METHOD_KEY . '" value="' . NoScriptLogin::METHOD_NUMBER . '"\/>.*/', $loginMethod->showHTMLLoginForm());
    }

}