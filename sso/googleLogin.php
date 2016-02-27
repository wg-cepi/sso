<?php
session_start();
require_once 'Autoloader.php';

use ModuleSSO\EndPoint\LoginMethod\ThirdParty\GoogleLogin;
use ModuleSSO\BrowserSniffer;
use Symfony\Component\HttpFoundation\Request;

BrowserSniffer::init();
Database::init();
$loginMethod = new GoogleLogin(Request::createFromGlobals());
$loginMethod->setOnCodeRequest();