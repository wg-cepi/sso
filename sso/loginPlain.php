<?php
/**
 * Endpoint for AJAX login requests
 */
session_start();
require_once 'Autoloader.php';

use ModuleSSO\EndPoint;
use ModuleSSO\BrowserSniffer;
use ModuleSSO\EndPoint\LoginMethod\Other\CORSLogin;
use Symfony\Component\HttpFoundation\Request;

BrowserSniffer::init();
Database::init();

$request = Request::createFromGlobals();
$endPoint = new EndPoint($request);
$endPoint->setLoginMethod(new CORSLogin($request));
$endPoint->run();