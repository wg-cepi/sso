<?php
namespace ModuleSSO\Client\LoginHelper\HTTP;

use ModuleSSO\Client\LoginHelper;

abstract class HTTPHelper extends LoginHelper
{
    public function appendStyles()
    {
        return '<link rel="stylesheet" href="' . CFG_SSO_URL . '/css/styles.css">';
    }
    
}