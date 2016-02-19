<?php

$I = new AcceptanceTester($scenario);
$I->amOnPage('/index.php?f=1');

$I->click('Login with SSO');
$I->see('Webgarden SSO');

$I->submitForm('#id-sso-form', array(
    'email' => 'joe@example.com',
    'password' => 'joe'
));
$I->see('User info');

