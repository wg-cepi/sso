<?php

$I = new AcceptanceTester($scenario);
$I->amOnPage('/index.php?f=2');

$I->switchToIFrame("id-iframe-login");
$I->submitForm('#id-sso-form', array(
    'email' => 'joe@example.com',
    'password' => 'joe'
));

//parent page
$I->switchToIFrame();
$I->wait(1);
$I->seeInSource('User info');
