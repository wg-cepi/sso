<?php
namespace ModuleSSO\Client\LoginHelper\HTTP;

use ModuleSSO\Client\LoginHelper;

/**
 * Class HTTPHelper
 * @package ModuleSSO\Client\LoginHelper\HTTP
 */
abstract class HTTPHelper extends LoginHelper
{
    /**
     * {@inheritdoc}
     * @return string
     */
    public function appendStyles()
    {
        return '<link rel="stylesheet" href="' . CFG_SSO_URL . '/css/styles.css">';
    }
    
}