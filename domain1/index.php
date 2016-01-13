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
    </head>
    <body>
        <h1><a href="<?php echo CFG_DOMAIN_URL ?>"><?php echo CFG_DOMAIN_DISPLAY_NAME ?></a></h1>
        <nav>
            <ul>
                <li>
                    <a href="./cross.php">Login crossroads</a>
                </li>
                <li>
                   <a href="./?logout=1">Local logout</a>
                </li>
                <li>
                    <a href="./?glogout=1">Global logout</a>
                </li>
            </ul>
        </nav>
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
    </body>
</html>
