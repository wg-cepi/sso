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
     * Checks if browser supports given login helper, true by default
     * @return bool
     *
     */
    public function isSupported()
    {
        return true;
    }


    /**
     * Passes parameter for rendering of login element to renderer
     *
     * @param string $continue
     *
     */
    public function showLogin($continue = '')
    {
        $this->renderer->renderLogin(array('continueUrl' => $continue));
    }
}

