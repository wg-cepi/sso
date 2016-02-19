<?php

/**
 * Class Database
 */
class Database
{
    /** @var \PDO $pdo  */
    public static $pdo = null;

    /**
     * Initializes database environment
     * If no parameters are passed, parameters from config are used
     *
     * @param string $dbName Name of database
     * @param string $sqlHost Name of host
     * @param string $user Name of user
     * @param string $password User's password
     */
    public static function init($dbName = CFG_SQL_DBNAME, $sqlHost = CFG_SQL_HOST, $user = CFG_SQL_USERNAME, $password = CFG_SQL_PASSWORD) {
        $dsn = 'mysql:dbname=' . $dbName . ';host=' . $sqlHost . '';
        try {
            $pdo = new PDO($dsn, $user, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$pdo = $pdo;
        } catch (PDOException $e) {
            die('Connection failed: ' . $e->getMessage());
        }
    }
}