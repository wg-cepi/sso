<?php
namespace ModuleSSO\Client\LoginHelper\HTTP;

/**
 * Class IframeHelper
 * @package ModuleSSO\Client\LoginHelper\HTTP
 */
class IframeHelper extends HTTPHelper
{
    /**
     * {@inheritdoc}
     * @param string $continue
     */
    public function showLogin($continue = '')
    {
        $this->renderer->renderLogin(array('continueUrl' => $continue));
    }

    /**
     * {@inheritdoc}
     * @return string
     */
    public function appendStyles()
    {
        return '<link rel="stylesheet" href="' . CFG_SSO_URL . '/css/iframe.styles.css">';
    }
}