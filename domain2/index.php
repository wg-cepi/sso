<?php
session_start();
require_once '../sso/app/config/config.inc.php';
require_once '../sso/app/module_sso/module_sso.php';

require_once 'app/config/config.inc.php';

$client = new Client();
$client->pickLoginMethod();
?>

<html>
    <head>
        <title><?php echo CFG_DOMAIN_DISPLAY_NAME ?></title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <?php $client->appendScripts(); ?>
        <link rel="stylesheet" href="css/material.min.css">
        <link rel="stylesheet" href="css/styles.css">
        
        <script src="js/material.min.js"></script>
        <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
        <link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Roboto:300,400,500,700" type="text/css">
    </head>
    <body>
        <div class="mdl-layout mdl-js-layout mdl-layout--fixed-header">
            <header class="mdl-layout__header">
                <div class="mdl-layout__header-row">
                  <!-- Title -->
                    <span class="mdl-layout-title"><?php echo CFG_DOMAIN_DISPLAY_NAME ?></span>
                    <!-- Add spacer, to align navigation to the right -->
                    <div class="mdl-layout-spacer"></div>
                    <!-- Navigation. We hide it in small screens. -->
                    <nav class="mdl-navigation mdl-layout--large-screen-only">
                        <a class="mdl-navigation__link" href="/">Home</a>
                       <a class="mdl-navigation__link" href="/cross.php">Login crossroads</a>
                       <a class="mdl-navigation__link" href="/?logout=1">Local logout</a>
                       <a class="mdl-navigation__link" href="/?glogout=1">Global logout</a>
                    </nav>
                </div>
            </header>
            <div class="mdl-layout__drawer">
                <span class="mdl-layout-title"><?php echo CFG_DOMAIN_DISPLAY_NAME ?></span>
                <nav class="mdl-navigation">
                    <a class="mdl-navigation__link" href="/">Home</a>
                    <a class="mdl-navigation__link" href="/cross.php">Login crossroads</a>
                    <a class="mdl-navigation__link" href="/?logout=1">Local logout</a>
                    <a class="mdl-navigation__link" href="/?glogout=1">Global logout</a>
                </nav>
            </div>
            <main class="mdl-layout__content">
                <div class="page-content">
                    <div class="grid-centered">
                        <div id="id-messages">
                            <?php echo $client->showMessages(); ?>
                        </div>
                        <h1><?php echo CFG_DOMAIN_DISPLAY_NAME ?> homepage</h1>
                        <section id="id-client-info">
                            <?php if (isset($_SESSION['uid'])): ?>

                            <h2>User info</h2>
                            <ul>
                                <li id="id-user-id">ID: <?php echo $_SESSION['uid'] ?></li>
                                <li>Email: <?php echo "TODO" ?></li>
                            </ul>
                            <?php else: ?>
                                <noscript><meta http-equiv="refresh" content="0;url=noscript.php"></noscript>
                            <?php endif ?>
                        </section>
                        <section id="id-client-login">
                            <?php 
                            if (!isset($_SESSION['uid'])) {
                                $client->showLoginMethodHTML();
                            }
                            $client->run();
                            ?>
                        </section>
                    </div>
                </div>
            </main>
        </div>
    </body>
</html>
