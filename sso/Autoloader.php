<?php
require_once __DIR__ . '/vendor/autoload.php';
define('APPDIR', __DIR__ . '/app');

global $dirs;
$dirs = array(
    APPDIR . '/',
    /*
    PREFIX . 'app/ModuleSSO/',
    PREFIX . 'app/ModuleSSO/method/',
    PREFIX . 'app/ModuleSSO/method/3rd-party/',
    PREFIX . 'app/ModuleSSO/method/3rd-party/facebook/',
    PREFIX . 'app/ModuleSSO/method/3rd-party/google/',
    
    PREFIX . 'app/ModuleSSO/method/classic/',
    PREFIX . 'app/ModuleSSO/method/classic/direct/',
    PREFIX . 'app/ModuleSSO/method/classic/noscript/',
    PREFIX . 'app/ModuleSSO/method/classic/iframe/',
    
    PREFIX . 'app/ModuleSSO/method/cors/',*/
    
    
    APPDIR . '/ModuleSSO/',
    APPDIR . '/ModuleSSO/Client/',
    APPDIR . '/ModuleSSO/EndPoint/',
    APPDIR . '/ModuleSSO/EndPoint/LoginMethod/',
    APPDIR . '/ModuleSSO/EndPoint/LoginMethod/HTTP/',
    APPDIR . '/ModuleSSO/EndPoint/LoginMethod/Other/',
    APPDIR . '/ModuleSSO/EndPoint/LoginMethod/ThirdParty/',
    APPDIR . '/ModuleSSO/Client/LoginHelper/',
    APPDIR . '/ModuleSSO/Client/LoginHelper/HTTP/',
    APPDIR . '/ModuleSSO/Client/LoginHelper/Other/',
    APPDIR . '/ModuleSSO/Client/LoginHelper/ThirdParty/',
    
    
);
function autoloadConfigAndUtils()
{
    $filename = APPDIR . "/config/config.php";
    if (is_readable($filename) && file_exists($filename)) {
        require_once ($filename);
    }
    
    $filename = APPDIR . "/Utils.php";
    if (is_readable($filename) && file_exists($filename)) {
        require_once ($filename);
    }
}
function autoloadModuleSSO($fullClassName) {
    $className = getClassName($fullClassName);

    global $dirs;
    foreach ($dirs as $dir) {
        $filename = $dir . $className . ".php";
        if (is_readable($filename) && file_exists($filename)) {
            require_once ($filename);
            return;
        }
    }
}

spl_autoload_register('autoloadConfigAndUtils');
spl_autoload_register('autoloadModuleSSO');