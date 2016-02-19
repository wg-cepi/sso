<?php

//login as joe@example.com
require 'successCept.php';

$I->amOnUrl('http://domain2.local/?f=3');
$I->wait(1);
$I->click('Log in as another user');

$I->wait(1);
$I->submitForm('#id-sso-form', array(
    'email' => 'bob@example.com',
    'password' => 'bob'
));

//on domain2.local
$I->wait(1);
$I->see('bob@example.com');
