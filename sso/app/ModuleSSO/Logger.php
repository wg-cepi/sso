<?php

class Logger {
    public static function log($what, $path = 'C:/wamp/logs/ssoLog.txt') {
        $fp = fopen($path, "a+");
        fwrite($fp, print_r($what, true). "\n");
        fclose($fp);
    }
}