<?php
namespace ModuleSSO\EndPoint\LoginMethod\Renderer\HTML;

/**
 * Class CORSLoginRenderer
 * @package ModuleSSO\EndPoint\LoginMethod\Renderer\HTML
 */
class CORSLoginRenderer extends HTMLRendererFactory
{
    /**
     * {@inheritdoc}
     * @param array $params
     * @return void
     */
    public function renderLoginForm($params = array())
    {
        echo '';
    }

    /**
     * {@inheritdoc}
     * @param array $params
     * @return void
     */
    public function renderContinueOrRelog($params = array())
    {
        echo '';
    }
}