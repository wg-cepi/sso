<?php
session_start();
require_once 'Autoloader.php';

use ModuleSSO\LoginMethod\ThirdPartyLogin\GoogleLogin;

Database::init();
$loginMethod = new GoogleLogin();
$loginMethod->redirectAndLogin();