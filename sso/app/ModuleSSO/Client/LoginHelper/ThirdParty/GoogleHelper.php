<?php
namespace ModuleSSO\Client\LoginHelper\ThirdParty;

/**
 * Class GoogleHelper
 * @package ModuleSSO\Client\LoginHelper\ThirdParty
 */
class GoogleHelper extends ThirdPartyHelper
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
