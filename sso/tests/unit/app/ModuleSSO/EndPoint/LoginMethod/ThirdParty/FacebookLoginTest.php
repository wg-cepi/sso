<?php

use ModuleSSO\EndPoint\LoginMethod\ThirdParty\FacebookLogin;
use Symfony\Component\HttpFoundation\Request;

class FacebookLoginTest extends PHPUnit_Framework_TestCase
{
    public function testSetOnLoginRequest()
    {
        //prepare continue URL
        $loginMethod = $this->getMockBuilder('ModuleSSO\EndPoint\LoginMethod\ThirdParty\FacebookLogin')
            ->setConstructorArgs(array(Request::createFromGlobals()))
            ->setMethods(array('redirect'))
            ->getMock();

        $loginMethod->expects($this->at(0))
            ->method('redirect');

        $loginMethod->setOnLoginRequest();
    }

    public function testRedirectWithToken()
    {
        //data in dummy database
        $query = \Database::$pdo->prepare("SELECT users.id, users.email, " . FacebookLogin::TABLE_COLUMN . " FROM users JOIN " . FacebookLogin::TABLE . " ON users.id = " . FacebookLogin::TABLE . ".user_id");
        $query->execute();
        $user = $query->fetch();

        $loginMethod = $this->getMockBuilder('ModuleSSO\EndPoint\LoginMethod\ThirdParty\FacebookLogin')
            ->setConstructorArgs(array(Request::createFromGlobals()))
            ->setMethods(array('redirect', 'setOrUpdateSSOCookie'))
            ->getMock();

        $loginMethod->expects($this->at(0))
            ->method('setOrUpdateSSOCookie')
            ->with($user['id']);

        $loginMethod->redirectWithToken($user[FacebookLogin::TABLE_COLUMN], $user['email']);
    }
}