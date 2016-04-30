<?php

use \ModuleSSO\EndPoint\LoginMethod\HTTP\NoScriptLogin;
use ModuleSSO\Cookie;
use Symfony\Component\HttpFoundation\Request;
use  \ModuleSSO\EndPoint\LoginMethod\Renderer\HTML\HTMLRendererFactory;

class HTTPLoginTest extends PHPUnit_Framework_TestCase
{
    public function testSetOnLoginRequestFailed()
    {
        //our test user
        //email: joe@example.com
        //password: joe
        //id: 1
        $_GET['email'] = 'joe@example.com';
        $_GET['password'] = 'badpassword';

        //any class that uses HTTPLogin::loginListener()
        $loginMethod = new NoScriptLogin(Request::createFromGlobals());
        $loginMethod->setRenderer(new \ModuleSSO\EndPoint\LoginMethod\Renderer\HTML\NoScriptLoginRenderer());
        $loginMethod->setOnLoginRequest();

        $this->expectOutputRegex('/.*Login failed, please try again.*/');
    }

    public function testSetOnLoginRequestContinueAsUser()
    {
        //prepare date
        $_GET[\ModuleSSO::LOGIN_KEY] = 1;
        $_COOKIE[Cookie::SECURE_SSO_COOKIE] = 'i am set';

        //any class that uses HTTPLogin::loginListener()
        $loginMethod = $this->getMockBuilder('ModuleSSO\EndPoint\LoginMethod\HTTP\NoScriptLogin')
            ->setConstructorArgs(array(Request::createFromGlobals()))
            ->setMethods(array('getUserFromCookie', 'generateTokenAndRedirect'))
            ->getMock();

        $loginMethod->expects($this->at(0))
            ->method('getUserFromCookie')->willReturn(true);

        $loginMethod->expects($this->at(1))
            ->method('generateTokenAndRedirect');

        $loginMethod->setOnLoginRequest();
    }

    public function testSetOnLoginRequestRelog()
    {
        //prepare data
        $_GET[\ModuleSSO::RELOG_KEY] = 1;

        //any class that uses HTTPLogin::loginListener()
        $loginMethod = $this->getMockBuilder('ModuleSSO\EndPoint\LoginMethod\HTTP\NoScriptLogin')
            ->setConstructorArgs(array(Request::createFromGlobals()))
            ->setMethods(array('showLoginForm'))
            ->getMock();

        $loginMethod->setRenderer(new \ModuleSSO\EndPoint\LoginMethod\Renderer\HTML\NoScriptLoginRenderer());

        $loginMethod->expects($this->once())
            ->method('showLoginForm');

        $loginMethod->setOnLoginRequest();

    }

    public function testShowHTML()
    {
        //1. User is in cookie
        //any class that uses HTTPLogin::showHTML()
        $loginMethod = $this->getMockBuilder('ModuleSSO\EndPoint\LoginMethod\HTTP\NoScriptLogin')
            ->setConstructorArgs(array(Request::createFromGlobals()))
            ->setMethods(array('getUserFromCookie'))
            ->getMock();

        $loginMethod->setRenderer(new \ModuleSSO\EndPoint\LoginMethod\Renderer\HTML\NoScriptLoginRenderer());

        $loginMethod->method('getUserFromCookie')->willReturn(array('id' => 1, 'email' => 'joe@example.com'));
        $loginMethod->expects($this->any())
            ->method('getUserFromCookie');

        $loginMethod->showHTML();


        $this->expectOutputRegex('/.*joe@example\.com.*Log in as another user.*/');
    }

    public function testShowHTML2()
    {
        //2. User is not in cookie
        //any class that uses HTTPLogin::showHTML()
        $loginMethod = $this->getMockBuilder('ModuleSSO\EndPoint\LoginMethod\HTTP\NoScriptLogin')
            ->setConstructorArgs(array(Request::createFromGlobals()))
            ->setMethods(array('getUserFromCookie'))
            ->getMock();

        $loginMethod->setRenderer(new \ModuleSSO\EndPoint\LoginMethod\Renderer\HTML\NoScriptLoginRenderer());

        $loginMethod->method('getUserFromCookie')->willReturn(null);

        $loginMethod->expects($this->any())
            ->method('getUserFromCookie');

        $loginMethod->showHTML();

        $this->expectOutputRegex('/.*Login to Webgarden SSO.*Email.*Password.*/');
    }


}
