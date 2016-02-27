<?php
session_start();
require_once 'Autoloader.php';

use ModuleSSO\EndPoint\LoginMethod\ThirdParty\FacebookLogin;
use ModuleSSO\BrowserSniffer;
use Symfony\Component\HttpFoundation\Request;

BrowserSniffer::init();
Database::init();
$loginMethod = new FacebookLogin(Request::createFromGlobals());
$loginMethod->setOnCodeRequest();