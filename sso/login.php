<?php
session_start();
require_once 'Autoloader.php';

use ModuleSSO\EndPoint;
use \ModuleSSO\BrowserSniffer;


BrowserSniffer::init();
Database::init();
$endPoint = new EndPoint();
$endPoint->pickLoginMethod();

?>

<html>
    <head>
        <title><?php echo CFG_SSO_DISPLAY_NAME ?></title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link rel="stylesheet" href="css/material.min.css">
        <link rel="stylesheet" href="css/common.styles.css">
        <?php echo $endPoint->appendStyles() ?>
        
        <script src="js/material.min.js"></script>
        <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
        <link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Roboto:300,400,500,700" type="text/css">
    </head>
    <body>
        <div class="sso">
            <div class="grid-centered">
                <?php $endPoint->run(); ?>
            </div>
        </div>
    </body>
</html>
