<?php

use ModuleSSO\EndPoint\LoginMethod\HTTP\NoScriptLogin;
use ModuleSSO\Cookie;
use Symfony\Component\HttpFoundation\Request;

class LoginMethodTest extends PHPUnit_Framework_TestCase
{
    public function testSetOnContinueUrlRequest()
    {
        $loginMethod = new NoScriptLogin(Request::createFromGlobals());
        //1. domain in whitelist
        $_GET[\ModuleSSO::CONTINUE_KEY] = 'http://domain1.local/test.php?param=X';
        $loginMethod->request = Request::createFromGlobals();
        $loginMethod->setOnContinueUrlRequest();

        $this->assertEquals('http://domain1.local/test.php', $loginMethod->getContinueUrl());
        $this->assertEquals('domain1.local', $loginMethod->getDomain());

        //2. subdomain in whitelist and exists in db
        $_GET[\ModuleSSO::CONTINUE_KEY] = 'http://sub1.domain1.local/sub.php?param=X';
        $loginMethod->request = Request::createFromGlobals();
        $loginMethod->setOnContinueUrlRequest();
        $this->assertEquals('http://sub1.domain1.local/sub.php', $loginMethod->getContinueUrl());
        $this->assertEquals('sub1.domain1.local', $loginMethod->getDomain());

        //3. subdomain in whitelist and does not in db
        $_GET[\ModuleSSO::CONTINUE_KEY] = 'http://sub2.domain2.local/sub.php?param=X';
        $loginMethod->request = Request::createFromGlobals();
        $loginMethod->setOnContinueUrlRequest();
        $this->assertEquals('http://sub2.domain2.local/sub.php', $loginMethod->getContinueUrl());
        $this->assertEquals('domain2.local', $loginMethod->getDomain());

        //4. domain is not in whitelist
        $_GET[\ModuleSSO::CONTINUE_KEY] = 'http://blacklisted.local/sub.php?param=X';
        $loginMethod->request = Request::createFromGlobals();
        $loginMethod->setOnContinueUrlRequest();
        $this->assertEquals(CFG_SSO_ENDPOINT_URL, $loginMethod->getContinueUrl());
        $this->assertEquals(CFG_JWT_ISSUER, $loginMethod->getDomain());

        //5. domain is malformed
        $_GET[\ModuleSSO::CONTINUE_KEY] = 'http://blacklisted/sub.php?param=X';
        $loginMethod->request = Request::createFromGlobals();
        $loginMethod->setOnContinueUrlRequest();
        $this->assertEquals(CFG_SSO_ENDPOINT_URL, $loginMethod->getContinueUrl());
        $this->assertEquals(CFG_JWT_ISSUER, $loginMethod->getDomain());

        //6. domain is malformed
        $_GET[\ModuleSSO::CONTINUE_KEY] = 'blacklisted/sub.php?param=X';
        $loginMethod->request = Request::createFromGlobals();
        $loginMethod->setOnContinueUrlRequest();
        $this->assertEquals(CFG_SSO_ENDPOINT_URL, $loginMethod->getContinueUrl());
        $this->assertEquals(CFG_JWT_ISSUER, $loginMethod->getDomain());

        //7. url in SESSION
        unset($_GET[\ModuleSSO::CONTINUE_KEY]);
        $_SESSION[\ModuleSSO::CONTINUE_KEY] = 'http://domain1.local/some/url';
        $loginMethod->request = Request::createFromGlobals();
        $loginMethod->setOnContinueUrlRequest();
        $this->assertEquals('http://domain1.local/some/url', $loginMethod->getContinueUrl());
        $this->assertEquals('domain1.local', $loginMethod->getDomain());

        //8. url in HTTP_REFERER
        unset($_SESSION[\ModuleSSO::CONTINUE_KEY]);
        $_SERVER['HTTP_REFERER'] = 'http://domain1.local/some/url';
        $loginMethod->request = Request::createFromGlobals();
        $loginMethod->setOnContinueUrlRequest();
        $this->assertEquals('http://domain1.local/some/url', $loginMethod->getContinueUrl());
        $this->assertEquals('domain1.local', $loginMethod->getDomain());
    }

    public function testGetUserFromCookie()
    {
        $request = Request::createFromGlobals();
        //pick any login method
        /** @var NoScriptLogin $loginMethod */
        $loginMethod = $this->getMockBuilder('ModuleSSO\EndPoint\LoginMethod\HTTP\NoScriptLogin')
            ->setConstructorArgs(array($request))
            ->setMethods(array('setOrUpdateSSOCookie'))
            ->getMock();

        //prepare test data
        $query = Database::$pdo->prepare("SELECT * FROM users ORDER BY id LIMIT 1");
        $query->execute();
        $user = $query->fetch();

        //1. test valid cookie
        $_COOKIE[Cookie::SECURE_SSO_COOKIE] = $user['cookie'];
        $loginMethod->request =  Request::createFromGlobals();
        $result = $loginMethod->getUserFromCookie();
        $this->assertEquals($user, $result);

        //2. test bad cookie
        $_COOKIE[Cookie::SECURE_SSO_COOKIE] = 'bad:cookie';
        $loginMethod->request =  Request::createFromGlobals();
        $result = $loginMethod->getUserFromCookie();
        $this->assertEquals(null, $result);

        //2. test malformed cookie
        $_COOKIE[Cookie::SECURE_SSO_COOKIE] = 'malformed';
        $loginMethod->request =  Request::createFromGlobals();
        $result = $loginMethod->getUserFromCookie();
        $this->assertEquals(null, $result);

    }

    public function testSetOnLogoutRequest()
    {
        //prepare data
        $_GET[\ModuleSSO::LOGOUT_KEY] = 1;
        $request = Request::createFromGlobals();

        //any class that uses HTTPLogin::loginListener()
        $loginMethod = $this->getMockBuilder('ModuleSSO\EndPoint\LoginMethod\HTTP\NoScriptLogin')
            ->setConstructorArgs(array($request))
            ->setMethods(array('redirect', 'unsetSSOCookie'))
            ->getMock();

        $loginMethod->expects($this->at(0))
            ->method('unsetSSOCookie');

        $loginMethod->expects($this->at(1))
            ->method('redirect');

        $loginMethod->setOnLogoutRequest($request);
    }

    public function testPerform()
    {
        //pick any login method that uses LoginMethod::perform()
        $loginMethod = $this->getMockBuilder('ModuleSSO\EndPoint\LoginMethod\HTTP\NoScriptLogin')
            ->setConstructorArgs(array(Request::createFromGlobals()))
            ->setMethods(array('setOnContinueUrlRequest', 'setOnLoginRequest', 'setOnLogoutRequest'))
            ->getMock();

        $loginMethod->expects($this->at(0))
            ->method('setOnContinueUrlRequest');

        $loginMethod->expects($this->at(1))
            ->method('setOnLoginRequest');

        $loginMethod->expects($this->at(2))
            ->method('setOnLogoutRequest');

        $loginMethod->perform();
    }

}
