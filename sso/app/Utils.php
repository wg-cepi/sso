<?php

/**
 * Converts fully classified class name to simple name
 *
 * @param string $fullClassName
 * @return string
 */
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

/**
 * Dumps variable, wrapped by HTML <pre> tag
 *
 * @param $var
 */
function print_pre($var) {
    echo "<pre>";
    print_r($var);
    echo "</pre>";
}