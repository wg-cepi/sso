<?php

use \ModuleSSO\EndPoint\LoginMethod\ThirdParty\GoogleLogin;

class GoogleLoginTest extends PHPUnit_Framework_TestCase
{
    public function testLoginListener()
    {
        //prepare continue URL
        $loginMethod = $this->getMockBuilder('ModuleSSO\EndPoint\LoginMethod\ThirdParty\GoogleLogin')
            ->setMethods(array('redirect'))
            ->getMock();

        $loginMethod->expects($this->at(0))
            ->method('redirect');

        $loginMethod->loginListener();
    }

    public function testRedirectWithToken()
    {
        //data in dummy database
        $query = \Database::$pdo->prepare("SELECT users.id, users.email, " . GoogleLogin::TABLE_COLUMN . " FROM users JOIN " . GoogleLogin::TABLE . " ON users.id = " . GoogleLogin::TABLE . ".user_id");
        $query->execute();
        $user = $query->fetch();

        $loginMethod = $this->getMockBuilder('ModuleSSO\EndPoint\LoginMethod\ThirdParty\GoogleLogin')
            ->setMethods(array('redirect', 'setOrUpdateSSOCookie'))
            ->getMock();

        $loginMethod->expects($this->at(0))
            ->method('setOrUpdateSSOCookie')
            ->with($user['id']);

        $loginMethod->redirectWithToken($user[GoogleLogin::TABLE_COLUMN], $user['email']);
    }
}