<?php
session_start();
require_once 'Autoloader.php';

use ModuleSSO\EndPoint\LoginMethod\ThirdParty\GoogleLogin;

Database::init();
$loginMethod = new GoogleLogin();
$loginMethod->redirectAndLogin();