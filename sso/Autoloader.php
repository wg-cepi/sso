<?php
require_once __DIR__ . '/vendor/autoload.php';
define('PREFIX', __DIR__ . '/');

global $dirs;
$dirs = array(
    PREFIX . 'app/',
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
    
    
    PREFIX . 'app/ModuleSSO/',
    PREFIX . 'app/ModuleSSO/Client/',
    PREFIX . 'app/ModuleSSO/EndPoint/',
    
    PREFIX . 'app/ModuleSSO/EndPoint/LoginMethod/',
    
    PREFIX . 'app/ModuleSSO/EndPoint/LoginMethod/HTTP/',
    PREFIX . 'app/ModuleSSO/EndPoint/LoginMethod/Other/',
    PREFIX . 'app/ModuleSSO/EndPoint/LoginMethod/ThirdParty/',
    
    PREFIX . 'app/ModuleSSO/Client/LoginHelper/',
    
    PREFIX . 'app/ModuleSSO/Client/LoginHelper/HTTP/',
    PREFIX . 'app/ModuleSSO/Client/LoginHelper/Other/',
    PREFIX . 'app/ModuleSSO/Client/LoginHelper/ThirdParty/',
    
    
);
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