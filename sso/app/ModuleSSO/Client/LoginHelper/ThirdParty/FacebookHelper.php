<?php
namespace ModuleSSO\Client\LoginHelper\ThirdParty;

/**
 * Class FacebookHelper
 * @package ModuleSSO\Client\LoginHelper\ThirdParty
 */
class FacebookHelper extends ThirdPartyHelper
{
    /**
     * {@inheritdoc}
     * @param string $continue
     */
    public function showLogin($continue = '')
    {
       $this->renderer->renderLogin();
    }
}
