<?php

class CORSLoginTest extends PHPUnit_Framework_TestCase
{
    public function testLoginListener()
    {
        //our test user
        //email: joe@example.com
        //password: joe
        //id: 1
        $_GET['email'] = 'joe@example.com';
        $_GET['password'] = 'joe';

        $_SERVER['HTTP_ORIGIN'] = 'domain1.local';

        $loginMethod = $this->getMockBuilder('ModuleSSO\EndPoint\LoginMethod\Other\CORSLogin')
            ->setMethods(array('setOrUpdateSSOCookie', 'verifyPasswordHash'))
            ->getMock();
        $loginMethod->method('verifyPasswordHash')->willReturn(true);

        $loginMethod->expects($this->at(0))
            ->method('verifyPasswordHash');

        $loginMethod->expects($this->at(1))
            ->method('setOrUpdateSSOCookie')
            ->with($this->equalTo(1));


        $loginMethod->loginListener();
        $this->expectOutputRegex('/\{"status":"ok","' . ModuleSSO::TOKEN_KEY . '":.*\}/');
    }

    public function testLoginListenerHTTPOriginNotSet()
    {
        //just for sure
        unset($_SERVER['HTTP_ORIGIN']);

        $loginMethod = new \ModuleSSO\EndPoint\LoginMethod\Other\CORSLogin();

        $loginMethod->loginListener();
        $this->expectOutputString(json_encode(array("status" => "fail", "code" => "http_origin_not_set")));
    }

    public function testPerform()
    {

    }

    public function testPerformDomainNotInWhiteList()
    {

    }

    public function testPerformCheckCookie()
    {

    }

    public function testPerformLogin()
    {

    }



}