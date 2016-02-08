<?php
session_start();
require_once 'Autoloader.php';

use ModuleSSO\EndPoint\LoginMethod\ThirdParty\GoogleLogin;
use ModuleSSO\BrowserSniffer;

BrowserSniffer::init();
Database::init();
$loginMethod = new GoogleLogin();
$loginMethod->redirectAndLogin();