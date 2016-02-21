<?php
// This is global bootstrap for autoloading
define('PROJECT_ROOT', 'C:/wamp/www/sso');
require_once PROJECT_ROOT . '/sso/Autoloader.php';
\Database::init('sso_dummy');