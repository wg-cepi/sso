<?php
namespace ModuleSSO\Client\LoginHelper\Renderer\HTML;
use ModuleSSO\Client\LoginHelper;
use ModuleSSO\Client\LoginHelper\Renderer\IRenderer;

use ModuleSSO\Client\LoginHelper\HTTP\NoScriptHelper;
use ModuleSSO\Client\LoginHelper\HTTP\IframeHelper;
use ModuleSSO\Client\LoginHelper\Other\CORSHelper;
use ModuleSSO\Client\LoginHelper\ThirdParty\FacebookHelper;
use ModuleSSO\Client\LoginHelper\ThirdParty\GoogleHelper;

/**
 * Class EHelperRendererNotBound
 * @package ModuleSSO\Client\LoginHelper\Renderer\HTML
 */
class EHelperRendererNotBound extends \Exception {}

/**
 * Class HTMLRenderer
 * @package ModuleSSO\Client\LoginHelper\Renderer\HTML
 */
class HTMLRendererFactory implements IRenderer
{
    /**
     * {@inheritdoc}
     * @param array $params
     */
    public function renderLogin($params = array())
    {
        $html = '';
        include 'Template/LoginTemplate.php';
        echo $html;
    }

    /**
     * {@inheritdoc}
     * @param LoginHelper $loginHelper
     * @return CORSHelperRenderer|FacebookHelperRenderer|GoogleHelperRenderer|IframeHelperRenderer|NoScriptHelperRenderer
     * @throws EHelperRendererNotBound()
     */
    public function getRenderer(LoginHelper $loginHelper)
    {
        if($loginHelper instanceof NoScriptHelper) {
            return new NoScriptHelperRenderer();
        } else if ($loginHelper instanceof IframeHelper) {
            return new IframeHelperRenderer();
        } else if($loginHelper instanceof CORSHelper) {
            return new CORSHelperRenderer();
        } else if($loginHelper instanceof FacebookHelper) {
            return new FacebookHelperRenderer();
        } else if($loginHelper instanceof GoogleHelper) {
            return new GoogleHelperRenderer();
        } else {
            throw new EHelperRendererNotBound();
        }
    }
}