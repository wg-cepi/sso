<?php
namespace ModuleSSO\Client\LoginHelper\Renderer;

use ModuleSSO\Client\LoginHelper;

/**
 * Class HelperRenderException
 * @package ModuleSSO\Client\LoginHelper\Renderer
 */
class HelperRenderException extends \Exception {}

/**
 * Interface IRenderer
 * @package ModuleSSO\Client\LoginHelper\Renderer
 */
interface IRenderer
{
    /**
     * Renders login element
     *
     * @param array $params
     * @return mixed
     */
    public function renderLogin($params = array());

    /**
     * Selects renderer for LoginHelper
     *
     * @param LoginHelper $loginHelper
     * @return mixed
     */
    public function selectRenderer(LoginHelper $loginHelper);
}