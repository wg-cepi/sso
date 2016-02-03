<?php
session_start();
require_once 'Autoloader.php';

use ModuleSSO\EndPoint\LoginMethod\ThirdParty\FacebookLogin;

Database::init();
$loginMethod = new FacebookLogin();
$loginMethod->redirectAndLogin();