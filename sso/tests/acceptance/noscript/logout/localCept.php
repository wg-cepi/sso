<?php
//login as joe@example.com
require PROJECT_ROOT . '/sso/tests/acceptance/noscript/login/successCept.php';

$I->maximizeWindow();
$I->click('Local logout');
$I->wait(1); //test bug fix wait
$I->amOnPage('/?f=1');
$I->seeInSource('Login with SSO'); //regular see method did't work

//check if user can "Continue as ..." on domain2.local
$I->amOnUrl('http://domain2.local/?f=1');
$I->click('Login with SSO');
$I->see('Continue as joe@example.com');

