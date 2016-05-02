<?php
/**
 * Class JavaScriptRedirectResponse
 *
 * Pseudo-redirect with JavaScript window.parent.location
 */
class JavaScriptRedirectResponse extends \Symfony\Component\HttpFoundation\RedirectResponse
{
    public function __toString()
    {
        return "<script>window.parent.location = '" . $this->targetUrl . "';</script>";
    }

    /**
     * Echos instance of JavaScriptRedirectResponse.
     *
     * @uses __toString()
     */
    public function send()
    {
        echo $this;
    }

}