<?php
namespace ModuleSSO\Client;

use ModuleSSO\Client\LoginHelper\Renderer\IRenderer;
/**
 * Class LoginHelper
 * @package ModuleSSO\Client
 */
abstract class LoginHelper
{
    /**
     * @var IRenderer
     */
    public $renderer = null;

    /**
     * Shows login HTML login form
     *
     * @param string $continue URL where user should continue in login process
     * @return mixed
     *
     * @uses ModuleSSO\Messages::showMessages()
     */
    abstract public function showLogin($continue = '');

    /**
     * Method for appending JavaScript scripts to HTML
     *
     * @return string
     */
    public function appendScripts()
    {
        return '';
    }

    /**
     * Method for appending CSS styles to HTML
     *
     * @return string
     */
    public function appendStyles()
    {
        return '';
    }

    /**
     * Checks if browser supports given login helper
     * @return bool
     *
     * @uses ModuleSSO\BrowserSniffer
     */
    public function isSupported()
    {
        return true;
    }
}

