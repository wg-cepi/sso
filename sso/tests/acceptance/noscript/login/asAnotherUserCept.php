<?php

//login as joe@example.com
require 'successCept.php';

$I->amOnUrl('http://domain2.local/?f=1');
$I->click('Login with SSO');

//http://sso.local
$I->click('Log in as another user');

$I->submitForm('#id-sso-form', array(
    'email' => 'bob@example.com',
    'password' => 'bob'
));

//on domain2.local
$I->see('bob@example.com');
