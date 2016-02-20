<?php

use ModuleSSO\EndPoint\LoginMethod\HTTP\NoScriptLogin;
use ModuleSSO\Cookie;

class LoginMethodTest extends PHPUnit_Framework_TestCase
{
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

        //5. domain is malformed
        $loginMethod = new NoScriptLogin();
        $_GET[\ModuleSSO::CONTINUE_KEY] = 'http://blacklisted/sub.php?param=X';
        $loginMethod->continueUrlListener();
        $this->assertEquals(CFG_SSO_ENDPOINT_URL, $loginMethod->getContinueUrl());
        $this->assertEquals(CFG_JWT_ISSUER, $loginMethod->getDomain());

        //6. domain is malformed
        $loginMethod = new NoScriptLogin();
        $_GET[\ModuleSSO::CONTINUE_KEY] = 'blacklisted/sub.php?param=X';
        $loginMethod->continueUrlListener();
        $this->assertEquals(CFG_SSO_ENDPOINT_URL, $loginMethod->getContinueUrl());
        $this->assertEquals(CFG_JWT_ISSUER, $loginMethod->getDomain());

        //7. url in SESSION
        unset($_GET[\ModuleSSO::CONTINUE_KEY]);
        $loginMethod = new NoScriptLogin();
        $_SESSION[\ModuleSSO::CONTINUE_KEY] = 'http://domain1.local/some/url';
        $loginMethod->continueUrlListener();
        $this->assertEquals('http://domain1.local/some/url', $loginMethod->getContinueUrl());
        $this->assertEquals('domain1.local', $loginMethod->getDomain());

        //8. url in HTTP_REFERER
        unset($_SESSION[\ModuleSSO::CONTINUE_KEY]);
        $_SERVER['HTTP_REFERER'] = 'http://domain1.local/some/url';
        $loginMethod = new NoScriptLogin();
        $loginMethod->continueUrlListener();
        $this->assertEquals('http://domain1.local/some/url', $loginMethod->getContinueUrl());
        $this->assertEquals('domain1.local', $loginMethod->getDomain());
    }

    public function testGetUserFromCookie()
    {
        //pick any login method
        /** @var NoScriptLogin $loginMethod */
        $loginMethod = $this->getMockBuilder('ModuleSSO\EndPoint\LoginMethod\HTTP\NoScriptLogin')
            ->setMethods(array('setAndUpdateSSOCookie'))
            ->getMock();

        //prepare test data
        $query = Database::$pdo->prepare("SELECT * FROM users ORDER BY id LIMIT 1");
        $query->execute();
        $user = $query->fetch();

        //1. test valid cookie
        $_COOKIE[Cookie::SECURE_SSO_COOKIE] = $user['cookie'];
        $result = $loginMethod->getUserFromCookie();
        $this->assertEquals($user, $result);

        //2. test bad cookie
        $_COOKIE[Cookie::SECURE_SSO_COOKIE] = 'bad:cookie';
        $result = $loginMethod->getUserFromCookie();
        $this->assertEquals(null, $result);

        //2. test malformed cookie
        $_COOKIE[Cookie::SECURE_SSO_COOKIE] = 'malformed';
        $result = $loginMethod->getUserFromCookie();
        $this->assertEquals(null, $result);

    }

    public function testLogoutListener()
    {
        //prepare data
        $_GET[\ModuleSSO::LOGOUT_KEY] = 1;

        //any class that uses HTTPLogin::loginListener()
        $loginMethod = $this->getMockBuilder('ModuleSSO\EndPoint\LoginMethod\HTTP\NoScriptLogin')
            ->setMethods(array('redirect', 'unsetSSOCookie'))
            ->getMock();

        $loginMethod->expects($this->at(0))
            ->method('unsetSSOCookie');

        $loginMethod->expects($this->at(1))
            ->method('redirect');

        $loginMethod->logoutListener();
    }

    public function testPerform()
    {
        //pick any login method that uses LoginMethod::perform()
        $loginMethod = $this->getMockBuilder('ModuleSSO\EndPoint\LoginMethod\HTTP\NoScriptLogin')
            ->setMethods(array('continueUrlListener', 'loginListener', 'logoutListener'))
            ->getMock();

        $loginMethod->expects($this->at(0))
            ->method('continueUrlListener');

        $loginMethod->expects($this->at(1))
            ->method('loginListener');

        $loginMethod->expects($this->at(2))
            ->method('logoutListener');

        $loginMethod->perform();

    }

}
