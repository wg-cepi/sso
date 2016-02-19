<?php

/**
 * Class Logger
 */
class Logger
{
    /**
     * Writes input parameter to log file
     *
     * @param string $what
     * @param string $path
     */
    public static function log($what, $path = 'C:/wamp/logs/ssoLog.txt') {
        $fp = fopen($path, "a+");
        fwrite($fp, print_r($what, true). "\n");
        fclose($fp);
    }
}