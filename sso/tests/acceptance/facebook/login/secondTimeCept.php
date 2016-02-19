<?php

$I = new AcceptanceTester($scenario);
$I->amOnPage('/index.php?f=4');

$I->click('//a[@href="http://sso.local/login.php?continue=http://domain1.local&m=4"]');
$I->submitForm('#login_form',
    array(
        'email' => 'testsso@wgz.cz',
        'pass' => 'test1234'
    )
);

$I->see('User info');