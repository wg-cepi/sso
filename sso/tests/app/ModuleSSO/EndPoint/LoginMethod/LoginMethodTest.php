<?php
session_start();
use ModuleSSO\EndPoint\LoginMethod\HTTP\NoScriptLogin;
class LoginMethoTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        \Database::init();
        \ModuleSSO\BrowserSniffer::init();
    }

    public function testContinueUrlListener()
    {
        //1. domain in whitelist
        $loginMethod = new NoScriptLogin();
        $_GET[\ModuleSSO::CONTINUE_KEY] = 'http://domain1.local/test.php?param=X';
        $loginMethod->continueUrlListener();
        $this->assertEquals('http://domain1.local/test.php', $loginMethod->getContinueUrl());
        $this->assertEquals('domain1.local', $loginMethod->getDomain());

        //2. subdomain in whitelist and exists in db
        $loginMethod = new NoScriptLogin();
        $_GET[\ModuleSSO::CONTINUE_KEY] = 'http://sub1.domain1.local/sub.php?param=X';
        $loginMethod->continueUrlListener();
        $this->assertEquals('http://sub1.domain1.local/sub.php', $loginMethod->getContinueUrl());
        $this->assertEquals('sub1.domain1.local', $loginMethod->getDomain());

        //3. subdomain in whitelist and does not in db
        $loginMethod = new NoScriptLogin();
        $_GET[\ModuleSSO::CONTINUE_KEY] = 'http://sub2.domain2.local/sub.php?param=X';
        $loginMethod->continueUrlListener();
        $this->assertEquals('http://sub2.domain2.local/sub.php', $loginMethod->getContinueUrl());
        $this->assertEquals('domain2.local', $loginMethod->getDomain());

        //4. domain is not in whitelist
        $loginMethod = new NoScriptLogin();
        $_GET[\ModuleSSO::CONTINUE_KEY] = 'http://blacklisted.local/sub.php?param=X';
        $loginMethod->continueUrlListener();
        $this->assertEquals(CFG_SSO_ENDPOINT_URL, $loginMethod->getContinueUrl());
        $this->assertEquals(CFG_JWT_ISSUER, $loginMethod->getDomain());

        //4. domain is malformed
        $loginMethod = new NoScriptLogin();
        $_GET[\ModuleSSO::CONTINUE_KEY] = 'http://blacklisted/sub.php?param=X';
        $loginMethod->continueUrlListener();
        $this->assertEquals(CFG_SSO_ENDPOINT_URL, $loginMethod->getContinueUrl());
        $this->assertEquals(CFG_JWT_ISSUER, $loginMethod->getDomain());

        //4.1 domain is malformed
        $loginMethod = new NoScriptLogin();
        $_GET[\ModuleSSO::CONTINUE_KEY] = 'blacklisted/sub.php?param=X';
        $loginMethod->continueUrlListener();
        $this->assertEquals(CFG_SSO_ENDPOINT_URL, $loginMethod->getContinueUrl());
        $this->assertEquals(CFG_JWT_ISSUER, $loginMethod->getDomain());
    }

    public function testPerform()
    {
        /*
        $loginMethodMock = $this->getMockBuilder('\ModuleSSO\EndPoint\LoginMethod\HTTP\NoScriptLogin')
            ->setMethods(array('showHTML'))
            ->getMock();

        $loginMethodMock->expects($this->once())->method('perform');

        $loginMethodMock->perform();
        */

    }

}
