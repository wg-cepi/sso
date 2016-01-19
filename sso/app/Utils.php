<?php

function redirect($url, $code = 303) {
    http_response_code($code);
    header("Location: " . $url);
    exit;
}

function getClassName($fullClassName)
{
    $pos = 0;
    $offset = 1;
    while($pos = strpos($fullClassName, '\\', $offset))
    {
        $offset = $pos + 1;
    }
    if($offset != 1) {
        return substr($fullClassName, $offset);
    } else {
        return $fullClassName;
    }
}
