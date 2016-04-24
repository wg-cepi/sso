# Webgarden SSO
## Setup
Also works on Linux, you will need LAMP or PHP + MySQL + Apache.

1. Edit hosts file and add following entry

    ```text
    127.0.0.1 domain1.local domain2.local sso.local
    ```
    
2. Install WAMP
    * assume, that you have installed WAMP to `C:/wamp`
    * you can create virtual domains (each in separated file) in `C:/wamp/vhosts` folder and include them in `httpd.conf` with `IncludeOptional "C:/wamp/vhosts/*"`

3. Modify virtual domains in `httpd.conf` or create them in `vhosts` folder

    ```text
    <VirtualHost *:80>
      DocumentRoot "C:/wamp/www/sso/domain2"
      ServerName domain2.local
      ServerAlias *.domain2.local domain2.local
    </VirtualHost>
    
    <VirtualHost *:80>
      DocumentRoot "C:/wamp/www/sso/domain1"
      ServerName domain1.local
      ServerAlias *.domain1.local domain1.local
    </VirtualHost>
    
    <VirtualHost *:80>
      DocumentRoot "C:/wamp/www/sso/sso"
      ServerName sso.local
    </VirtualHost>
    ```

4. Create a file `googleLogin.php` in `C:/wamp/www` with following contents. This will redirect response from Google API to Webgarden SSO Endpoint

    ```php
    <?php
    //C:/wamp/www/googleLogin.php
    if(isset($_GET['code'])) {
    	$code = $_GET['code'];
    	header("Location: http://sso.local/googleLogin.php?code=" . $code);
    	exit;
    }
    ```

5. In `sso/config.php` configure
    * `CFG_SQL_HOST`
    * `CFG_SQL_DBNAME`
    * `CFG_SQL_USERNAME`
    * `CFG_SQL_PASSWORD`

6. Create database (use name in `CFG_SQL_DBNAME`) and populate it with following SQL

    ```sql
    -- Adminer 4.2.3 MySQL dump
    
    SET NAMES utf8;
    SET time_zone = '+00:00';
    SET foreign_key_checks = 0;
    SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';
    
    DROP TABLE IF EXISTS `domains`;
    CREATE TABLE `domains` (
     `id` int(11) NOT NULL AUTO_INCREMENT,
     `name` varchar(32) COLLATE utf8_bin NOT NULL,
     `user_id` int(11) DEFAULT NULL,
     PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
    
    INSERT INTO `domains` (`id`, `name`, `user_id`) VALUES
    (1,	'domain1.local',	NULL),
    (2,	'domain2.local',	NULL),
    (3,	'sub1.domain1.local',	NULL);
    
    DROP TABLE IF EXISTS `tokens`;
    CREATE TABLE `tokens` (
     `id` int(11) NOT NULL AUTO_INCREMENT,
     `value` varchar(1024) COLLATE utf8_bin NOT NULL,
     `used` tinyint(1) NOT NULL,
     `expires` int(11) NOT NULL,
     `domain_id` int(11) NOT NULL,
     PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
    
    
    DROP TABLE IF EXISTS `users`;
    CREATE TABLE `users` (
     `id` int(11) NOT NULL AUTO_INCREMENT,
     `email` text COLLATE utf8_bin NOT NULL,
     `password` varchar(512) COLLATE utf8_bin NOT NULL,
     `first_name` varchar(64) COLLATE utf8_bin NOT NULL,
     `last_name` varchar(64) COLLATE utf8_bin NOT NULL,
     `cookie` varchar(256) COLLATE utf8_bin NOT NULL,
     PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
    
    INSERT INTO `users` (`id`, `email`, `password`, `first_name`, `last_name`, `cookie`) VALUES
    (1,	'joe@example.com',	'$1$k84.dI3.$9/qnahwUbk3047whNEojD/',	'Joe',	'Satriani',	'26ccbe440ff6f04144d0ef78dfa252fa:c67e09386ae1834b9dffcc157bcadfeb'),
    (2,	'bob@example.com',	'$1$Js2.eI3.$HFmO/0rNJp9Yts/HU99YQ1',	'Bob',	'Jackson',	'296292edf0553ca52e51f2fb284cf731:d0e7c070617b13fcdfe8340479d72437'),
    (3,	'testsso@wgz.cz',	'',	'',	'',	'e659afb8dff710c3ba7cfe665453cc21:baa52ff7190baa9f574ff802778e0ddf');
    
    DROP TABLE IF EXISTS `user_login_facebook`;
    CREATE TABLE `user_login_facebook` (
     `id` int(11) NOT NULL AUTO_INCREMENT,
     `user_id` int(11) NOT NULL,
     `facebook_id` decimal(30,0) NOT NULL,
     `created` int(11) NOT NULL,
     PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
    
    INSERT INTO `user_login_facebook` (`id`, `user_id`, `facebook_id`, `created`) VALUES
    (1,	3,	107712179619654,	1456008427);
    
    DROP TABLE IF EXISTS `user_login_google`;
    CREATE TABLE `user_login_google` (
     `id` int(11) NOT NULL AUTO_INCREMENT,
     `user_id` int(11) NOT NULL,
     `google_id` decimal(30,0) NOT NULL,
     `created` int(11) NOT NULL,
     PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
    
    INSERT INTO `user_login_google` (`id`, `user_id`, `google_id`, `created`) VALUES
    (1,	3,	106440411057598425368,	1456008298);
    
    -- 2016-03-09 22:53:19
    ```

6. Access http://sso.local. You should see Webgarden SSO endpoint.
7. You have 3 pre-created users
    1. Email: joe@example.com, pass: joe
    2. Email: bob@example.com, pass bob
    3. User for FB or Google login
        * email: testsso@wgz.cz
        * google pass: test1234//
        * facebook pass: test1234

## Playing with SSO
### Login scenario
1. Access http://domain1.local and login
    * Email: joe@example.com
    * Password: joe
2. Access http://domain2.local
    1. You will see **"Continue as joe@example.com"**
    2. Click **"Continue as ..."**
    3. Now you are logged in as joe@example.com
        * You are on different domain
        * You did not have to enter your credentials again

### Local logout scenario
1. Login http://domain1.local
2. Press **"Local logout"** button
3. Go to http://domain2.local
4. Login at http://domain2.local
5. Now you are logged in at http://domain2.local
    * You are not logged in at http://domain1.local but you have logged in there
    
### Global logout scenario
1. Login http://domain1.local
2. Navigate to http://domain2.local
3. You will see **"Continue as ..."**
4. Navigate to http://domain1.local
5. Press **"Global logout"** button
6. Navigate to http://domain2.local
7. You WON'T see **"Continue as ..."**, because you have logged yourself from SSO