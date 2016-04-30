<?php

namespace ModuleSSO\EndPoint\LoginMethod\Renderer;

use ModuleSSO\EndPoint\LoginMethod;

interface IRenderer
{
    /**
     * Renders login form
     *
     * @param array $params
     * @return mixed
     */
    public function renderLoginForm($params = array());

    /**
     * Renders element for 'continuing as user'
     *
     * @param array $params
     * @return mixed
     */
    public function renderContinueOrRelog($params = array());

    /**
     * Selects renderer for LoginMethod
     *
     * @param LoginMethod $loginMethod
     * @return mixed
     */
    public function getRenderer(LoginMethod $loginMethod);
}