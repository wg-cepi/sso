<?php
/**
 * Endpoint for HTTP and ThirdParty login requests
 */
session_start();
require_once 'Autoloader.php';

use ModuleSSO\EndPoint;
use ModuleSSO\BrowserSniffer;
use ModuleSSO\EndPoint\LoginMethod\ThirdParty\FacebookLogin;
use ModuleSSO\EndPoint\LoginMethod\ThirdParty\GoogleLogin;
use Symfony\Component\HttpFoundation\Request;

BrowserSniffer::init();
Database::init();
$endPoint = new EndPoint(Request::createFromGlobals());
$endPoint->pickLoginMethod();

$fbLoginUrl = CFG_SSO_ENDPOINT_URL . '?' . \ModuleSSO::CONTINUE_KEY . '=' . CFG_SSO_ENDPOINT_INDEX_URL . '&' . \ModuleSSO::METHOD_KEY . '=' . FacebookLogin::METHOD_NUMBER;
$googleLoginUrl = CFG_SSO_ENDPOINT_URL . '?' . \ModuleSSO::CONTINUE_KEY . '=' . CFG_SSO_ENDPOINT_INDEX_URL . '&' . \ModuleSSO::METHOD_KEY . '=' . GoogleLogin::METHOD_NUMBER;

$googleLoginLink = '<a class="mdl-navigation__link" href="' . $googleLoginUrl . '">Login with Google</a>';
$fbLoginLink = '<a class="mdl-navigation__link" href="' . $fbLoginUrl . '">Login with Facebook</a>';
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
                        <?php echo $googleLoginLink; ?>
                        <?php echo $fbLoginLink; ?>
                        <a class="mdl-navigation__link" href="/register.php">Register</a>
                    </nav>
                </div>
            </header>
            <div class="mdl-layout__drawer">
                <span class="mdl-layout-title"><?php echo CFG_SSO_DISPLAY_NAME ?></span>
                <nav class="mdl-navigation">
                    <a class="mdl-navigation__link" href="/index.php">Home</a>
                    <?php echo $googleLoginLink; ?>
                    <?php echo $fbLoginLink; ?>
                    <a class="mdl-navigation__link" href="/register.php">Register</a>
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
