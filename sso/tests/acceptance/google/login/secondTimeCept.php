<?php

$I = new AcceptanceTester($scenario);
$I->amOnPage('/index.php?f=5');

$I->click('//a[@href="http://sso.local/login.php?continue=http://domain1.local&m=5"]');

$I->submitForm('#gaia_loginform',
    array(
        'Email' => 'testsso@wgz.cz'
    )
);
$I->wait(1);
$I->submitForm('#gaia_loginform',
    array(
        'Passwd' => 'test1234//'
    )
);

//without auth prompt
$I->wait(2);
$I->see('User info');