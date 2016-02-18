<?php
use ModuleSSO\Client;
class ClientTest extends PHPUnit_Framework_TestCase
{
    private $publicKeyPath = 'app/../../domain1/app/config/pk.pub';
    public function setUp()
    {
        \Database::init();
        \ModuleSSO\BrowserSniffer::init();
    }

    public function testPickLoginHelper()
    {
        $client = new Client($this->publicKeyPath);

        //1. test forced pick
        $_GET[ModuleSSO::FORCED_METHOD_KEY] = 1;
        $client->pickLoginHelper();
        $this->assertInstanceOf('\ModuleSSO\Client\LoginHelper\HTTP\NoScriptHelper', $client->getLoginHelper());

        $_GET[ModuleSSO::FORCED_METHOD_KEY] = 2;
        $client->pickLoginHelper();
        $this->assertInstanceOf('\ModuleSSO\Client\LoginHelper\HTTP\IframeHelper', $client->getLoginHelper());

        $_GET[ModuleSSO::FORCED_METHOD_KEY] = 3;
        $client->pickLoginHelper();
        $this->assertInstanceOf('\ModuleSSO\Client\LoginHelper\Other\CORSHelper', $client->getLoginHelper());

        $_GET[ModuleSSO::FORCED_METHOD_KEY] = 4;
        $client->pickLoginHelper();
        $this->assertInstanceOf('\ModuleSSO\Client\LoginHelper\ThirdParty\FacebookHelper', $client->getLoginHelper());

        $_GET[ModuleSSO::FORCED_METHOD_KEY] = 5;
        $client->pickLoginHelper();
        $this->assertInstanceOf('\ModuleSSO\Client\LoginHelper\ThirdParty\GoogleHelper', $client->getLoginHelper());

        unset($_GET[ModuleSSO::FORCED_METHOD_KEY]);

        //2. test suppoerted browser pick
        $_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/48.0.2564.109 Safari/537.36';
        $client->pickLoginHelper();
        $this->assertInstanceOf('\ModuleSSO\Client\LoginHelper\Other\CORSHelper', $client->getLoginHelper());

        //3. test browser without CORS support
        unset($_SERVER['HTTP_USER_AGENT']);
        $client->pickLoginHelper();
        $this->assertInstanceOf('\ModuleSSO\Client\LoginHelper\HTTP\IframeHelper', $client->getLoginHelper());

    }

    public function testGetContinueUrl()
    {
        require_once __DIR__ . '/../../../../../../domain1/app/config/config.php';
        $client = new Client($this->publicKeyPath);

        //1. test empty request uri
        $_SERVER['REQUEST_URI'] = '';
        $this->assertEquals('http://domain1.local',$client->getContinueUrl());

        //2. test no path in uri
        $_SERVER['REQUEST_URI'] = '?param=foo';
        $this->assertEquals('http://domain1.local',$client->getContinueUrl());

        //3. test valid uri
        $_SERVER['REQUEST_URI'] = '/continue/here';
        $this->assertEquals('http://domain1.local/continue/here',$client->getContinueUrl());

        //4. test valid uri with param
        $_SERVER['REQUEST_URI'] = '/continue/here?cut=me';
        $this->assertEquals('http://domain1.local/continue/here',$client->getContinueUrl());

    }

    public function testGetUser()
    {
        $client = new Client($this->publicKeyPath);
        $query = \Database::$pdo->prepare("SELECT * FROM users WHERE id = 1");
        $query->execute();
        $testUser = $query->fetch();

        //1. test existing user
        $_SESSION['uid'] = 1;
        $this->assertEquals($testUser, $client->getUser());

        //2. test nonexistung user
        $_SESSION['uid'] = -1;
        $this->assertEquals(null, $client->getUser());

        //3. test empty uid
        unset($_SESSION['uid']);
        $this->assertEquals(null, $client->getUser());

    }

}