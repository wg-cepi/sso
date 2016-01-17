<?php
require_once 'app/module_sso/module_sso.php';
session_start();

$endPoint = new EndPoint();
$endPoint->pickLoginMethod();
$endPoint->run();
?>

