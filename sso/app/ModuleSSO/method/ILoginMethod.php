<?php
namespace ModuleSSO;

interface ILoginMethod
{
    /*
     * Takes care of login process
     */
    public function login();
}

