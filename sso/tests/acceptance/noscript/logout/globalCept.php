<?php

//login as joe@example.com
require PROJECT_ROOT . '/sso/tests/acceptance/noscript/login/successCept.php';

$I->click('Global logout');
$I->amOnPage('/index.php?f=1');
$I->seeInSource('Login with SSO');

//check if user can't "Continue as ..."
$I->amOnUrl('http://domain2.local/?f=1');
$I->click('Login with SSO');
$I->seeInSource('Email');
$I->seeInSource('Password');