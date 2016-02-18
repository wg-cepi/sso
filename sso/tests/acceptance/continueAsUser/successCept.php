<?php

//login first
$I = new AcceptanceTester($scenario);
$I->wantTo('Log in');
$I->amOnPage('/index.php?f=1');

$I->click('Login with SSO');
$I->see('Webgarden SSO');

$I->submitForm('#id-sso-form', array(
    'email' => 'joe@example.com',
    'password' => 'joe'
));

$I->see('User info');


//go to domain2 and try to login
$I->amOnPage('/toDomain2.php');
$I->see('Domain 2');

$I->wait(1);
$I->see('Continue as joe@example.com');

$I->click('Continue as joe@example.com');
$I->wait(1);
$I->see('User info');

//clean
$I->click('Global logout');

$I->amOnPage('/');
$I->click('Global logout');