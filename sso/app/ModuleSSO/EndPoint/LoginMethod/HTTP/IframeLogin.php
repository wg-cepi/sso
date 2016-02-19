<?php
namespace ModuleSSO\EndPoint\LoginMethod\HTTP;

/**
 * Class IframeLogin
 * @package ModuleSSO\EndPoint\LoginMethod\HTTP
 */
class IframeLogin extends HTTPLogin
{
    /**
     * @var string Number of login method
     */
    const METHOD_NUMBER = 2;

    /**
     * Performs JavaScript redirect
     * {@inheritdoc}
     */
    public function redirect($url = CFG_SSO_ENDPOINT_URL, $code = 302)
    {
        echo "<script>window.parent.location = '" . $url . "';</script>";
    }

    /**
     * {@inheritdoc}
     */
    public function showHTMLHeader()
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function appendStyles()
    {
        return '<link rel="stylesheet" href="css/iframe.styles.css">';
    }
}