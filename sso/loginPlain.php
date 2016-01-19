<?php
session_start();
require_once 'Autoloader.php';

use ModuleSSO\EndPoint;

Database::init();
$endPoint = new EndPoint();
$endPoint->pickLoginMethod();
$endPoint->run();

