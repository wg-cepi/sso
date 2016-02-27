<?php
namespace ModuleSSO\EndPoint;

/**
 * Interface ILoginMethod
 * @package ModuleSSO\EndPoint
 */
interface ILoginMethod
{
    /**
     * Takes care of login process
     */
    public function setOnLoginRequest();

    /**
     * Takes care of logout process
     */
    public function setOnLogoutRequest();

    /**
     * Obtains URL where user should continue
     */
    public function setOnContinueUrlRequest();

    /**
     * Starts lifecycle of LoginMethod
     */
    public function perform();
}

