<?php
session_start();
require_once '../sso/app/config/config.inc.php';
require_once 'app/config/config.inc.php';
?>

<html>
    <head>
        <title><?php echo CFG_DOMAIN_DISPLAY_NAME ?></title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
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
                        <h1>Login crossroads</h1>
                        <div class="mdl-grid">
                            <div class="mdl-cell mdl-cell--4-col">
                                <div class="demo-card-square mdl-card mdl-shadow--2dp">
                                    <div class="mdl-card__title mdl-card--expand">
                                        <h2 class="mdl-card__title-text">&lt;noscript&gt;</h2>
                                    </div>
                                    <div class="mdl-card__supporting-text">
                                        Lorem ipsum dolor sit amet, consectetur adipiscing elit.
                                        Aenan convallis.
                                    </div>
                                    <div class="mdl-card__actions mdl-card--border">
                                        <a href="/?f=1" class="mdl-button mdl-button--colored mdl-js-button mdl-js-ripple-effect">
                                          Login with NoScript
                                        </a>
                                    </div>
                                  </div>
                            </div>
                            <div class="mdl-cell mdl-cell--4-col">
                                <div class="demo-card-square mdl-card mdl-shadow--2dp">
                                    <div class="mdl-card__title mdl-card--expand">
                                        <h2 class="mdl-card__title-text">&lt;iframe&gt;</h2>
                                    </div>
                                    <div class="mdl-card__supporting-text">
                                        Lorem ipsum dolor sit amet, consectetur adipiscing elit.
                                        Aenan convallis.
                                    </div>
                                    <div class="mdl-card__actions mdl-card--border">
                                        <a href="/?f=2" class="mdl-button mdl-button--colored mdl-js-button mdl-js-ripple-effect">
                                          Login with Iframe
                                        </a>
                                    </div>
                                  </div>
                            </div>
                            <div class="mdl-cell mdl-cell--4-col">
                                <div class="demo-card-square mdl-card mdl-shadow--2dp">
                                    <div class="mdl-card__title mdl-card--expand">
                                        <h2 class="mdl-card__title-text">AJAX &amp; CORS</h2>
                                    </div>
                                    <div class="mdl-card__supporting-text">
                                        Lorem ipsum dolor sit amet, consectetur adipiscing elit.
                                        Aenan convallis.
                                    </div>
                                    <div class="mdl-card__actions mdl-card--border">
                                        <a href="/?f=3" class="mdl-button mdl-button--colored mdl-js-button mdl-js-ripple-effect">
                                          Login with AJAX &amp; CORS
                                        </a>
                                    </div>
                                  </div>
                            </div>
                        </div>                      
                    </div>
                </div>
            </main>
        </div>
    </body>
</html>
