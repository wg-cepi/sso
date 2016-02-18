<?php

$I = new AcceptanceTester($scenario);
$I->wantTo('Log in');
$I->amOnPage('/index.php?f=3');


$I->submitForm('#id-sso-form', array(
    'email' => 'BADLOGIN@bad.bad',
    'password' => 'BADLOGIN'
));
$I->wait(1);
$I->see('Login failed');
