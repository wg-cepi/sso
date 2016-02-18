<?php
session_start();
require_once 'Autoloader.php';

use ModuleSSO\Messages;
Database::init();

function createUserListener()
{
    if(isset($_GET['create']) && $_GET['create'] == 1) {
        if(!empty($_GET['email']) && !empty($_GET['password']) && !empty($_GET['fname']) && !empty($_GET['lname'])) {
            $email = trim($_GET['email']);
            $firstName = $_GET['fname'];
            $lastName = $_GET['lname'];
            $password = $_GET['password'];

            //input validation
            if(!filter_var($email, FILTER_VALIDATE_EMAIL) && $email !== '') {
                Messages::insert('Please, fill correct email address', 'warn');
                return;
            }
            if(trim($firstName) === '') {
                Messages::insert('First name can not be empty', 'warn');
                return;
            }

            if(trim($lastName) === '') {
                Messages::insert('Last name can not be empty', 'warn');
                return;
            }

            $query = \Database::$pdo->prepare("SELECT id FROM users WHERE email='$email'");
            $query->execute();
            $user = $query->fetch();
            //creating new user
            if(!$user){
                //password hash creation
                $loginMethod = new \ModuleSSO\EndPoint\LoginMethod\HTTP\DirectLogin();
                $hashedPassword = $loginMethod->generatePasswordHash($password);
                echo $hashedPassword;
                //insert user
                $query = \Database::$pdo->prepare("INSERT INTO users(email, password, first_name, last_name) VALUES ('$email', '$hashedPassword', '$firstName', '$lastName')");
                $query->execute();

                Messages::insert('User created');
            } else {
                Messages::insert('Email address is already used', 'warn');
            }
        } else {
            Messages::insert('Please, fill all required (*) fields', 'warn');
        }
    }
}

createUserListener();
?>

<html>
    <head>
        <title><?php echo CFG_SSO_DISPLAY_NAME ?></title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link rel="stylesheet" href="css/material.min.css">
        <link rel="stylesheet" href="css/common.styles.css">
        <link rel="stylesheet" href="css/styles.css">
        <script src="js/common.js"></script>
        <script src="js/material.min.js"></script>
        <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
        <link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Roboto:300,400,500,700" type="text/css">
    </head>
    <body>
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
            <main class="mdl-layout__content">
                <div class="page-content">
                    <div class="sso">
                        <div class="grid-centered">
                            <h1>Create user</h1>
                            <div id="id-login-area" class="mdl-card--border mdl-shadow--2dp">
                                <form id="id-sso-form">
                                     <div class="inputs">
                                        <div class="input-email mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                                            <input type="text" class="mdl-textfield__input" name="email" id="id-email"/>
                                            <label for="id-email" class="mdl-textfield__label">
                                                Email*
                                            </label>
                                        </div>
                                        <div class="input-pass mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                                            <label for="id-pass" class="mdl-textfield__label">
                                                Password*
                                            </label>
                                            <input type="password" class="mdl-textfield__input" name="password" id="id-pass"/>
                                        </div>
                                         <div class="input-pass mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                                             <label for="id-fname" class="mdl-textfield__label">
                                                 First name*
                                             </label>
                                             <input type="text" class="mdl-textfield__input" name="fname" id="id-fname"/>
                                         </div>
                                         <div class="input-pass mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                                             <label for="id-lname" class="mdl-textfield__label">
                                                 Last name*
                                             </label>
                                             <input type="text" class="mdl-textfield__input" name="lname" id="id-lname"/>
                                         </div>
                                    </div>
                                    <input type="hidden" name="create" value="1"/>
                                    <div class="button-wrap">
                                        <input type="submit" class="button-full mdl-button mdl-js-button mdl-button--raised" id="id-login-button" value="Create user"/>
                                    </div>
                                    <?php echo Messages::showMessages() ?>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>