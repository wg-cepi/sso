<?php

$I = new AcceptanceTester($scenario);
$I->wantTo('Log in');
$I->amOnPage('/index.php?f=2');

$I->switchToIFrame("id-iframe-login");
$I->submitForm('#id-sso-form', array(
    'email' => 'joe@example.com',
    'password' => 'joe'
));

//parent page
$I->switchToIFrame();
$I->see('User info');

$I->click('Global logout');