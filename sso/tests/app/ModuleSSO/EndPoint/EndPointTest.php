<?php
/**
 * Created by PhpStorm.
 * User: yadmin
 * Date: 03.02.2016
 * Time: 17:59
 */
class EndPointTest extends PHPUnit_Framework_TestCase
{

    public function testPickLoginMethod()
    {
        $endPoint = new ModuleSSO\EndPoint();

        $_GET['m'] = 1;
        $endPoint->pickLoginMethod();
        $this->assertInstanceOf('\ModuleSSO\EndPoint\LoginMethod\HTTP\NoScriptLogin', $endPoint->getLoginMethod());

        $_GET['m'] = 2;
        $endPoint->pickLoginMethod();
        $this->assertInstanceOf('\ModuleSSO\EndPoint\LoginMethod\HTTP\IframeLogin', $endPoint->getLoginMethod());

        $_GET['m'] = 3;
        $endPoint->pickLoginMethod();
        $this->assertInstanceOf('\ModuleSSO\EndPoint\LoginMethod\Other\CorsLogin', $endPoint->getLoginMethod());

        $_GET['m'] = 4;
        $endPoint->pickLoginMethod();
        $this->assertInstanceOf('\ModuleSSO\EndPoint\LoginMethod\ThirdParty\FacebookLogin', $endPoint->getLoginMethod());

        $_GET['m'] = 5;
        $endPoint->pickLoginMethod();
        $this->assertInstanceOf('\ModuleSSO\EndPoint\LoginMethod\ThirdParty\GoogleLogin', $endPoint->getLoginMethod());

        $_GET['m'] = 666;
        $endPoint->pickLoginMethod();
        $this->assertInstanceOf('\ModuleSSO\EndPoint\LoginMethod\HTTP\DirectLogin', $endPoint->getLoginMethod());

        unset($_GET['m']);
        $endPoint->pickLoginMethod();
        $this->assertInstanceOf('\ModuleSSO\EndPoint\LoginMethod\HTTP\DirectLogin', $endPoint->getLoginMethod());

    }

}
