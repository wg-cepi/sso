<?php
use Symfony\Component\HttpFoundation\Request;

class EndPointTest extends PHPUnit_Framework_TestCase
{

    public function testPickLoginMethod()
    {
        $endPoint = new ModuleSSO\EndPoint(Request::createFromGlobals());

        $_GET['m'] = 1;
        $endPoint->request = Request::createFromGlobals();
        $endPoint->pickLoginMethod();
        $this->assertInstanceOf('\ModuleSSO\EndPoint\LoginMethod\HTTP\NoScriptLogin', $endPoint->getLoginMethod());

        $_GET['m'] = 2;
        $endPoint->request = Request::createFromGlobals();
        $endPoint->pickLoginMethod();
        $this->assertInstanceOf('\ModuleSSO\EndPoint\LoginMethod\HTTP\IframeLogin', $endPoint->getLoginMethod());

        $_GET['m'] = 3;
        $endPoint->request = Request::createFromGlobals();
        $endPoint->pickLoginMethod();
        $this->assertInstanceOf('\ModuleSSO\EndPoint\LoginMethod\Other\CorsLogin', $endPoint->getLoginMethod());

        $_GET['m'] = 4;
        $endPoint->request = Request::createFromGlobals();
        $endPoint->pickLoginMethod();
        $this->assertInstanceOf('\ModuleSSO\EndPoint\LoginMethod\ThirdParty\FacebookLogin', $endPoint->getLoginMethod());

        $_GET['m'] = 5;
        $endPoint->request = Request::createFromGlobals();
        $endPoint->pickLoginMethod();
        $this->assertInstanceOf('\ModuleSSO\EndPoint\LoginMethod\ThirdParty\GoogleLogin', $endPoint->getLoginMethod());

        $_GET['m'] = 666;
        $endPoint->request = Request::createFromGlobals();
        $endPoint->pickLoginMethod();
        $this->assertInstanceOf('\ModuleSSO\EndPoint\LoginMethod\HTTP\DirectLogin', $endPoint->getLoginMethod());

        unset($_GET['m']);
        $endPoint->request = Request::createFromGlobals();
        $endPoint->pickLoginMethod();
        $this->assertInstanceOf('\ModuleSSO\EndPoint\LoginMethod\HTTP\DirectLogin', $endPoint->getLoginMethod());

    }

    public function testRun()
    {
        $request = Request::createFromGlobals();
        $endPoint = new ModuleSSO\EndPoint($request);

        $loginMethod = $this->getMockBuilder('ModuleSSO\EndPoint\LoginMethod\HTTP\NoScriptLogin')
            ->setConstructorArgs(array($request))
            ->setMethods(array('perform'))
            ->getMock();


        $endPoint->setLoginMethod($loginMethod);

        $loginMethod->expects($this->at(0))
            ->method('perform');

        $endPoint->run();
    }

}
