<?php

//login as joe@example.com
require 'successCept.php';

$I->amOnUrl('http://domain2.local/?f=2');
$I->switchToIFrame("id-iframe-login");
$I->click('Log in as another user');

$I->submitForm('#id-sso-form', array(
    'email' => 'bob@example.com',
    'password' => 'bob'
));

//on domain2.local
$I->wait(1);
$I->see('bob@example.com');
