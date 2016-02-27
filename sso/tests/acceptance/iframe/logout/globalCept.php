<?php

//login as joe@example.com
require PROJECT_ROOT . '/sso/tests/acceptance/iframe/login/successCept.php';

$I->maximizeWindow();
$I->click('Global logout');
$I->amOnPage('/index.php?f=2');
$I->wait(1);
$I->switchToIFrame("id-iframe-login");
$I->see('Login to Webgarden SSO');
$I->switchToIFrame();

//check if user can't "Continue as ..."
$I->amOnUrl('http://domain2.local/?f=2');
$I->wait(1);
$I->switchToIFrame("id-iframe-login");
$I->see('Email');
$I->see('Password');