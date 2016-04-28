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
     *
     * @uses JavaScriptRedirectResponse
     */
    public function redirect($url = CFG_SSO_ENDPOINT_URL, $code = 302)
    {
        echo \JavaScriptRedirectResponse::create($url)->send();
    }

    /**
     * {@inheritdoc}
     */
    public function appendStyles()
    {
        $links = '<link rel="stylesheet" href="http://' . $this->getDomain() . '/css/material.min.css">';
        $links .= '<link rel="stylesheet" href="css/iframe.styles.css">';
        return $links;

    }
}