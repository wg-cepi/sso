<?php
namespace ModuleSSO\EndPoint;

interface ILoginMethod
{
    /*
     * Takes care of login process
     */
    public function loginListener();
}

