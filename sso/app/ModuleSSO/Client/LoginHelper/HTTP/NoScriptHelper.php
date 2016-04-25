<?php
namespace ModuleSSO\Client\LoginHelper\HTTP;

/**
 * Class NoScriptHelper
 * @package ModuleSSO\Client\LoginHelper\HTTP
 */
class NoScriptHelper extends HTTPHelper
{
    /**
     * {@inheritdoc}
     *
     * @param string $continue
     */
    public function showLogin($continue = '')
    {
        $this->renderer->renderLogin(array('continueUrl' => $continue));
    }
}