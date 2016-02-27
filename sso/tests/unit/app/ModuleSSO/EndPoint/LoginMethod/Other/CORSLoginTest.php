<?php

use \ModuleSSO\EndPoint\LoginMethod\Other\CORSLogin;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class CORSLoginTest extends PHPUnit_Framework_TestCase
{
    public function testSetOnLoginRequest()
    {
        //our test user
        //email: joe@example.com
        //password: joe
        //id: 1
        $_GET['email'] = 'joe@example.com';
        $_GET['password'] = 'joe';

        $_SERVER['HTTP_ORIGIN'] = 'domain1.local';

        $loginMethod = new CORSLogin(Request::createFromGlobals());

        $loginMethod->setOnLoginRequest();
        $this->expectOutputRegex('/\{"status":"ok","' . ModuleSSO::TOKEN_KEY . '":.*\}/');
    }

    public function testSetOnCheckCookieRequestUserFound()
    {
        //prepare test data
        $_SERVER['HTTP_ORIGIN'] = 'http://domain1.local';
        $query = \Database::$pdo->prepare('SELECT * FROM users WHERE id=1');
        $query->execute();
        $user = $query->fetch();


        $loginMethod = $this->getMockBuilder('ModuleSSO\EndPoint\LoginMethod\Other\CORSLogin')
            ->setConstructorArgs(array(Request::createFromGlobals()))
            ->setMethods(array('getUserFromCookie'))
            ->getMock();

        $loginMethod->method('getUserFromCookie')->willReturn($user);

        $loginMethod->expects($this->at(0))
            ->method('getUserFromCookie');

        $loginMethod->setOnCheckCookieRequest();
        $this->expectOutputRegex('/\{"status":"ok","' . ModuleSSO::TOKEN_KEY . '":.*,"email":"' . $user['email'] . '"\}/');

    }

    public function testSetOnCheckCookieRequestBadCookie()
    {
        //prepare test data
        $_SERVER['HTTP_ORIGIN'] = 'http://domain1.local';

        $loginMethod = $this->getMockBuilder('ModuleSSO\EndPoint\LoginMethod\Other\CORSLogin')
            ->setConstructorArgs(array(Request::createFromGlobals()))
            ->setMethods(array('getUserFromCookie'))
            ->getMock();

        $loginMethod->method('getUserFromCookie')->willReturn(false);

        $loginMethod->expects($this->at(0))
            ->method('getUserFromCookie');

        $loginMethod->setOnCheckCookieRequest();
        $this->expectOutputString(JsonResponse::create(array("status" => "fail", "code" => "bad_cookie")));
    }

    public function testSetOnLoginRequestHTTPOriginNotSet()
    {
        //just for sure
        unset($_SERVER['HTTP_ORIGIN']);

        $loginMethod = new CORSLogin(Request::createFromGlobals());

        $loginMethod->setOnLoginRequest();
        $this->expectOutputString(json_encode(array("status" => "fail", "code" => "http_origin_not_set")));
    }

    public function testPerformLoginRequest()
    {
        //prepare test data
        $_GET = array();
        $_SERVER['HTTP_ORIGIN'] = 'http://domain1.local';
        $_GET[\ModuleSSO::LOGIN_KEY] = 1;

        $loginMethod = $this->getMockBuilder('ModuleSSO\EndPoint\LoginMethod\Other\CORSLogin')
            ->setConstructorArgs(array(Request::createFromGlobals()))
            //->setMethods(array('setOnLoginRequest'))
            ->getMock();

        $loginMethod->expects($this->at(0))
            ->method('setOnLoginRequest');

        $loginMethod->perform();
    }

    public function testPerformCheckCookieRequest()
    {
        //prepare test data
        $_GET = array();
        $_SERVER['HTTP_ORIGIN'] = 'http://domain1.local';
        $_GET[\ModuleSSO::CHECK_COOKIE_KEY] = 1;

        $loginMethod = $this->getMockBuilder('ModuleSSO\EndPoint\LoginMethod\Other\CORSLogin')
            ->setConstructorArgs(array(Request::createFromGlobals()))
            //->setMethods(array('setOnLoginRequest'))
            ->getMock();

        $loginMethod->expects($this->at(0))
            ->method('setOnLoginRequest');

        $loginMethod->perform();

    }

    public function testPerformDomainNotInWhiteList()
    {
        //prepare test data
        $_SERVER['HTTP_ORIGIN'] = 'http://baddomain.verybad';

        $loginMethod = new CORSLogin(Request::createFromGlobals());
        $loginMethod->perform();

        $this->expectOutputString('{"status":"fail","code":"domain_not_allowed"}');

    }

}