<?php

//login as joe@example.com
require PROJECT_ROOT . '/sso/tests/acceptance/noscript/login/successCept.php';

//go to domain2 and try to continue
$I->amOnUrl('http://domain2.local/?f=1');
$I->seeInSource('Login with SSO');
$I->click('Login with SSO');

$I->wait(1);
$I->see('Continue as joe@example.com');

$I->click('Continue as joe@example.com');
$I->wait(1);
$I->see('User info');