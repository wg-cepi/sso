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

    public function send()
    {
        echo $this;
    }

}