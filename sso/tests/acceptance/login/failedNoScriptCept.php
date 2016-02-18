<?php

$I = new AcceptanceTester($scenario);
$I->wantTo('Log in');
$I->amOnPage('/index.php?f=1');

$I->click('Login with SSO');
$I->see('Webgarden SSO');

$I->submitForm('#id-sso-form', array(
    'email' => 'BADLOGIN@bad.bad',
    'password' => 'BADLOGIN'
));

$I->see('Login failed');