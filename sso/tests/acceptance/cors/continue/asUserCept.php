<?php

//login as joe@example.com
require PROJECT_ROOT . '/sso/tests/acceptance/cors/login/successCept.php';

//go to domain2 and try to continue
$I->amOnUrl('http://domain2.local/?f=3');
$I->click('Continue as joe@example.com');
$I->wait(1);
$I->see('User info');