<?php
namespace ModuleSSO\Client\LoginHelper\Renderer;

use ModuleSSO\Client\LoginHelper;

class HelperRenderException extends \Exception {}

interface IRenderer
{
    public function renderLogin($params = array());

    public function selectRenderer(LoginHelper $loginHelper);
}