<?php

//login as joe@example.com
require PROJECT_ROOT . '/sso/tests/acceptance/noscript/login/successCept.php';

$I->maximizeWindow();
$I->click('Global logout');
$I->amOnPage('/?f=3');
$I->wait(1);
$I->see('Email');
$I->see('Password');

//check if user can't "Continue as ..."
$I->amOnUrl('http://domain2.local/?f=3');
$I->wait(1);
$I->see('Email');
$I->see('Password');