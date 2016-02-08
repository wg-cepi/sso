<?php
session_start();
require_once 'Autoloader.php';

use ModuleSSO\EndPoint;
use \ModuleSSO\BrowserSniffer;


BrowserSniffer::init();
Database::init();
$endPoint = new EndPoint();
$endPoint->pickLoginMethod();
$endPoint->run();

