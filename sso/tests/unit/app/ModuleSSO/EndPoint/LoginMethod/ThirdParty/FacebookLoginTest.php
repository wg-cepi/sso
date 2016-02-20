<?php

class FacebookLoginTest extends PHPUnit_Framework_TestCase
{
    public function testLoginListener()
    {
        //prepare continue URL
        $loginMethod = $this->getMockBuilder('ModuleSSO\EndPoint\LoginMethod\ThirdParty\FacebookLogin')
            ->setMethods(array('redirect'))
            ->getMock();

        $loginMethod->expects($this->at(0))
            ->method('redirect');

        $loginMethod->loginListener();
    }

    public function testRedirectWithToken()
    {
        //data in dummy database
        $socialId = 106440411057598425368;
        $socialEmail = 'testsso@wgz.cz';
        $query = \Database::$pdo->prepare('SELECT * FROM users WHERE id=25');
        $query->execute();
        $user = $query->fetch();

        $loginMethod = $this->getMockBuilder('ModuleSSO\EndPoint\LoginMethod\ThirdParty\GoogleLogin')
            ->setMethods(array('redirect', 'setOrUpdateSSOCookie'))
            ->getMock();

        $loginMethod->expects($this->at(0))
            ->method('setOrUpdateSSOCookie')
            ->with($user['id']);

        $loginMethod->redirectWithToken($socialId, $socialEmail);
    }
}