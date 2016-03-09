<?php
namespace ModuleSSO\EndPoint\LoginMethod\HTTP;

/**
 * Class DirectLogin
 * @package ModuleSSO\EndPoint\LoginMethod\HTTP
 */
class DirectLogin extends HTTPLogin
{
    /**
     * @var int Number of login method
     */
    const METHOD_NUMBER = 0;

    /**
     * {@inheritdoc}
     */
    public function showLoginForm()
    {
        $this->renderer->renderLoginForm();
    }

    /**
     * {@inheritdoc}
     */
    public function showContinueOrRelog($user)
    {
        $this->renderer->renderContinueOrRelog(array('user' => $user));
    }

    /**
     * {@inheritdoc}
     */
    protected function generateTokenAndRedirect($user)
    {
        $this->redirect(CFG_SSO_ENDPOINT_INDEX_URL);
    }
 
}

