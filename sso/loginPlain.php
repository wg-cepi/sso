<?php
//require_once 'app/ModuleSSO/ModuleSSO.php';
require_once 'app/config/config.php';
session_start();

use ModuleSSO\EndPoint;

$endPoint = new EndPoint();
$endPoint->pickLoginMethod();
$endPoint->run();

