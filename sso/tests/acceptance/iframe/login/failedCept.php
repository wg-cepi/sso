<?php

$I = new AcceptanceTester($scenario);
$I->amOnPage('/index.php?f=2');

$I->switchToIFrame("id-iframe-login");
$I->submitForm('#id-sso-form', array(
    'email' => 'BADLOGIN@bad.bad',
    'password' => 'BADLOGIN'
));

$I->see('Login failed');

