<?php

use \ModuleSSO\EndPoint\LoginMethod\HTTP\NoScriptLogin;
use ModuleSSO\Cookie;

class HTTPLoginTest extends PHPUnit_Framework_TestCase
{
    public function testLoginListener()
    {
        //our test user
        //email: joe@example.com
        //password: joe
        //id: 1
        $_GET['email'] = 'joe@example.com';
        $_GET['password'] = 'joe';

        //any class that uses HTTPLogin::loginListener()
        $loginMethod = $this->getMockBuilder('ModuleSSO\EndPoint\LoginMethod\HTTP\NoScriptLogin')
            ->setMethods(array('setOrUpdateSSOCookie', 'generateTokenAndRedirect', 'verifyPasswordHash'))
            ->getMock();
        $loginMethod->method('verifyPasswordHash')->willReturn(true);

        $loginMethod->expects($this->at(0))
            ->method('verifyPasswordHash');

        $loginMethod->expects($this->at(1))
            ->method('setOrUpdateSSOCookie')
            ->with($this->equalTo(1));

        $loginMethod->expects($this->at(2))
            ->method('generateTokenAndRedirect');

        $loginMethod->loginListener();
    }

    public function testLoginListenerContinue()
    {
        //prepare date
        $_GET[\ModuleSSO::LOGIN_KEY] = 1;
        $_COOKIE[Cookie::SECURE_SSO_COOKIE] = 'i am set';

        //any class that uses HTTPLogin::loginListener()
        $loginMethod = $this->getMockBuilder('ModuleSSO\EndPoint\LoginMethod\HTTP\NoScriptLogin')
            ->setMethods(array('getUserFromCookie', 'generateTokenAndRedirect'))
            ->getMock();

        $loginMethod->expects($this->at(0))
            ->method('getUserFromCookie')->willReturn(true);

        $loginMethod->expects($this->at(1))
            ->method('generateTokenAndRedirect');

        $loginMethod->loginListener();
    }

    public function testLoginListenerRelog()
    {
        //prepare data
        $_GET[\ModuleSSO::RELOG_KEY] = 1;

        //any class that uses HTTPLogin::loginListener()
        $loginMethod = $this->getMockBuilder('ModuleSSO\EndPoint\LoginMethod\HTTP\NoScriptLogin')
            ->setMethods(array('showHTMLLoginForm'))
            ->getMock();

        $loginMethod->expects($this->once())
            ->method('showHTMLLoginForm');

        $loginMethod->loginListener();


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
}
