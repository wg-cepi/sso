<?php

//login as joe@example.com
require 'successCept.php';

$I->amOnUrl('http://sso.local');
$I->grabCookie('SSOC');
