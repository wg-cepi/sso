<?php
/**
 * Endpoint for HTTP and ThirdParty login requests
 */
session_start();
require_once 'Autoloader.php';

use ModuleSSO\EndPoint;
use ModuleSSO\BrowserSniffer;

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
        <script src="js/common.js"></script>
        <?php echo $endPoint->appendStyles() ?>
        
        <script src="js/material.min.js"></script>
        <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
        <link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Roboto:300,400,500,700" type="text/css">
    </head>
    <body>
        <?php if($endPoint->getLoginMethod()->getMethodNumber() !== 2): ?>
        <div class="mdl-layout mdl-js-layout mdl-layout--fixed-header">
            <header class="mdl-layout__header">
                <div class="mdl-layout__header-row">
                    <!-- Title -->
                    <span class="mdl-layout-title"><?php echo CFG_SSO_DISPLAY_NAME ?></span>
                    <!-- Add spacer, to align navigation to the right -->
                    <div class="mdl-layout-spacer"></div>
                    <!-- Navigation. We hide it in small screens. -->
                    <nav class="mdl-navigation mdl-layout--large-screen-only">
                        <a class="mdl-navigation__link" href="/index.php">Home</a>
                        <a class="mdl-navigation__link" href="/createUser.php">Create User</a>
                    </nav>
                </div>
            </header>
            <div class="mdl-layout__drawer">
                <span class="mdl-layout-title"><?php echo CFG_SSO_DISPLAY_NAME ?></span>
                <nav class="mdl-navigation">
                    <a class="mdl-navigation__link" href="/index.php">Home</a>
                    <a class="mdl-navigation__link" href="/createUser.php">Create User</a>
                </nav>
            </div>
        <?php endif; ?>
            <?php if($endPoint->getLoginMethod()->getMethodNumber() !== 2): ?>
            <main class="mdl-layout__content">
                <div class="page-content">
            <?php endif; ?>
                    <div class="sso">
                        <div class="grid-centered">
                            <?php $endPoint->run(); ?>
                        </div>
                    </div>
            <?php if($endPoint->getLoginMethod()->getMethodNumber() !== 2): ?>
                </div>
            </main>
            <?php endif; ?>
        <?php if($endPoint->getLoginMethod()->getMethodNumber() !== 2): ?>
        </div>
        <?php endif; ?>
    </body>
</html>
