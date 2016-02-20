<?php

use \ModuleSSO\EndPoint\LoginMethod\Other\CORSLogin;

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

        $loginMethod = new CORSLogin();

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

    public function testPerformLoginListener()
    {
        //prepare test data
        $_GET = array();
        $_SERVER['HTTP_ORIGIN'] = 'http://domain1.local';
        $_GET[\ModuleSSO::LOGIN_KEY] = 1;

        $loginMethod = $this->getMockBuilder('ModuleSSO\EndPoint\LoginMethod\Other\CORSLogin')
            ->setMethods(array('loginListener'))
            ->getMock();

        $loginMethod->expects($this->at(0))
            ->method('loginListener');

        $loginMethod->perform();
    }

    public function testPerformDomainNotInWhiteList()
    {
        //prepare test data
        $_SERVER['HTTP_ORIGIN'] = 'http://baddomain.verybad';

        $loginMethod = new CORSLogin();
        $loginMethod->perform();

        $this->expectOutputString('{"status":"fail","code":"domain_not_allowed"}');

    }

}