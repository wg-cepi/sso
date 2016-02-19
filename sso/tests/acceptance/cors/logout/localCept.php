<?php
//login as joe@example.com
require PROJECT_ROOT . '/sso/tests/acceptance/cors/login/successCept.php';

$I->click('Local logout');
$I->wait(1); //test bug fix wait
$I->amOnPage('/?f=3');
$I->wait(1);
$I->see('Continue as joe@example.com'); //regular see method did't work

//check if user can "Continue as ..." on domain2.local
$I->amOnUrl('http://domain2.local/?f=3');
$I->wait(1);
$I->see('Continue as joe@example.com');

