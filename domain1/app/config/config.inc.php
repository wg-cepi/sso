<?php

define('CFG_SQL_HOST', 'localhost');
define('CFG_SQL_DBNAME', 'sso');
define('CFG_SQL_USERNAME', 'root');
define('CFG_SQL_PASSWORD', '');
define('CFG_JWT_AUD', 'http://domain1.local');
define('CFG_AUTH_SERVER', 'http://sso.local');

class Database {
    public static $pdo = null;
    public static function init() {
        $dsn = 'mysql:dbname=' . CFG_SQL_DBNAME . ';host=' . CFG_SQL_HOST . '';
        $user = CFG_SQL_USERNAME;
        $password = CFG_SQL_PASSWORD;
        
        try {
            $pdo = new PDO($dsn, $user, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$pdo = $pdo;
        } catch (PDOException $e) {
            die('Connection failed: ' . $e->getMessage());
        }
    }
}

