<?php
require_once __DIR__ . '/vendor/autoload.php';
define('PREFIX', __DIR__ . '/');

function autoLoadDatabase()
{
    $filename = PREFIX . "app/ModuleSSO/Database.php";
    if (is_readable($filename) && file_exists($filename)) {
        require_once ($filename);
    }
}
function autoloadConfigAndUtils()
{
    $filename = PREFIX . "app/config/config.php";
    if (is_readable($filename) && file_exists($filename)) {
        require_once ($filename);
    }
    
    $filename = PREFIX . "app/Utils.php";
    if (is_readable($filename) && file_exists($filename)) {
        require_once ($filename);
    }
}
function autoloadModuleSSO($fullClassName) {
    $className = getClassName($fullClassName);
    $filename = PREFIX . "app/ModuleSSO/" . $className . ".php";
    if (is_readable($filename) && file_exists($filename)) {
        require_once ($filename);
        return;
    }
    
    $filename = PREFIX . "app/ModuleSSO/method/" . $className . ".php";
    if (is_readable($filename) && file_exists($filename)) {
        require_once ($filename);
         return;
    }
    
    $filename = PREFIX . "app/ModuleSSO/method/3rd-party/" . $className . ".php";
    if (is_readable($filename) && file_exists($filename)) {
        require_once ($filename);
         return;
    }
    
    $filename = PREFIX . "app/ModuleSSO/method/classic/" . $className . ".php";
    if (is_readable($filename) && file_exists($filename)) {
        require_once ($filename);
         return;
    }
    
    $filename = PREFIX . "app/ModuleSSO/method/experimental/" . $className . ".php";
    if (is_readable($filename) && file_exists($filename)) {
        require_once ($filename);
         return;
    }
}

spl_autoload_register('autoloadConfigAndUtils');
spl_autoload_register('autoLoadDatabase');
spl_autoload_register('autoloadModuleSSO');