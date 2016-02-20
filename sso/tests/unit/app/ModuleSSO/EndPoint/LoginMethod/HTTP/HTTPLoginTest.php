<?php

use \ModuleSSO\EndPoint\LoginMethod\HTTP\NoScriptLogin;
use ModuleSSO\Cookie;

class HTTPLoginTest extends PHPUnit_Framework_TestCase
{
    public function testLoginListenerLoginFailed()
    {
        //our test user
        //email: joe@example.com
        //password: joe
        //id: 1
        $_GET['email'] = 'joe@example.com';
        $_GET['password'] = 'badpassword';

        //any class that uses HTTPLogin::loginListener()
        $loginMethod = new NoScriptLogin();
        $loginMethod->loginListener();

        $this->expectOutputRegex('/.*Login failed, please try again.*/');
    }

    public function testLoginListenerContinueAsUser()
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

    public function testShowHTML()
    {
        //1. User is in cookie
        //any class that uses HTTPLogin::showHTML()
        $loginMethod = $this->getMockBuilder('ModuleSSO\EndPoint\LoginMethod\HTTP\NoScriptLogin')
            ->setMethods(array('getUserFromCookie'))
            ->getMock();

        $loginMethod->method('getUserFromCookie')->willReturn(array('id' => 1, 'email' => 'joe@example.com'));

        $loginMethod->expects($this->at(0))
            ->method('getUserFromCookie');


        $this->assertRegexp('/.*joe@example\.com.*Log in as another user.*/', $loginMethod->showHTML());


        //2. User is not in cookie
        //any class that uses HTTPLogin::showHTML()
        $loginMethod = $this->getMockBuilder('ModuleSSO\EndPoint\LoginMethod\HTTP\NoScriptLogin')
            ->setMethods(array('getUserFromCookie'))
            ->getMock();

        $loginMethod->method('getUserFromCookie')->willReturn(null);

        $loginMethod->expects($this->at(0))
            ->method('getUserFromCookie');

        $this->assertRegexp('/.*Login to Webgarden SSO.*Email.*Password.*/', $loginMethod->showHTML());

    }


}
