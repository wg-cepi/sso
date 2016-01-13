<?php
session_start();
require_once '../sso/app/config/config.inc.php';
require_once 'app/config/config.inc.php';
?>

<html>
    <head>
        <title><?php echo CFG_DOMAIN_DISPLAY_NAME ?> - Crossroads</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    </head>
    <body>
        <h1><a href="<?php echo CFG_DOMAIN_URL ?>"><?php echo CFG_DOMAIN_DISPLAY_NAME ?></a></h1>
        <nav>
            <ul>
                <li><a href="./?f=1">NoScript method</a></li>
                <li><a href="./?f=2">Iframe method</a></li>
                <li><a href="./?f=3">AJAX + CORS method</a></li
            </ul>
        </nav>
    </body>
</html>
