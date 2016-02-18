<?php

$I = new AcceptanceTester($scenario);
$I->wantTo('Log in');
$I->amOnPage('/index.php?f=3');

$I->submitForm('#id-sso-form', array(
    'email' => 'joe@example.com',
    'password' => 'joe'
));
$I->wait(1);
$I->see('User info');

$I->click('Global logout');