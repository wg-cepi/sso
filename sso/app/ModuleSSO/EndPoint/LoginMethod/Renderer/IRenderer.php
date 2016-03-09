<?php

namespace ModuleSSO\EndPoint\LoginMethod\Renderer;

use ModuleSSO\EndPoint\LoginMethod;

interface IRenderer
{
    public function renderLoginForm($params = array());

    public function renderContinueOrRelog($params = array());

    public function selectRenderer(LoginMethod $loginMethod);
}