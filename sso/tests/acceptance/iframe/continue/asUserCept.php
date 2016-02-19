<?php

//login as joe@example.com
require PROJECT_ROOT . '/sso/tests/acceptance/iframe/login/successCept.php';

//go to domain2 and try to continue
$I->amOnUrl('http://domain2.local/?f=2');
$I->switchToIFrame("id-iframe-login");
$I->click('Continue as joe@example.com');
$I->wait(1);
$I->see('User info');