<?php
/**
 * Endpoint for AJAX login requests
 */
session_start();
require_once 'Autoloader.php';

use ModuleSSO\EndPoint;
use ModuleSSO\BrowserSniffer;
use ModuleSSO\EndPoint\LoginMethod\Other\CORSLogin;


BrowserSniffer::init();
Database::init();
$endPoint = new EndPoint();
$endPoint->setLoginMethod(new CORSLogin());
$endPoint->run();