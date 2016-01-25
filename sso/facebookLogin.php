<?php
session_start();
require_once 'Autoloader.php';

use ModuleSSO\LoginMethod\ThirdPartyLogin\FacebookLogin;

Database::init();
$loginMethod = new FacebookLogin();
$loginMethod->redirectAndLogin();