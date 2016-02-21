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
    public function loginListener();

    /**
     * Takes care of logout process
     */
    public function logoutListener();

    public function perform();
}

