<?php
namespace ModuleSSO\EndPoint\LoginMethod\ThirdParty;

use Symfony\Component\HttpFoundation\Request;

/**
 * Class GoogleLogin
 * @package ModuleSSO\EndPoint\LoginMethod\ThirdParty
 */
class GoogleLogin extends ThirdPartyLogin
{
    /**
     * @var int Number of login method
     */
    const METHOD_NUMBER = 5;

    /**
     * @var string Name of social pairing table
     */
    const TABLE = 'user_login_google';

    /**
     * @var string Name of column in social pairing table
     */
    const TABLE_COLUMN = 'google_id';

    /**
     * @var string Key for Google access token
     */
    const ACCESS_TOKEN_KEY = 'google_access_token';

    /**
     * @var \Google_Client
     */
    private $google;

    /**
     * GoogleLogin constructor
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);

        $this->google = new \Google_Client();
        $this->google->setClientId(CFG_G_CLIENT_ID);
        $this->google->setClientSecret(CFG_G_CLIENT_SECRET);
        $this->google->setRedirectUri(CFG_G_REDIRECT_URI);
    }

    /**
     * {@inheritdoc}
     */
    public function setOnLoginRequest()
    {
        $_SESSION[\ModuleSSO::CONTINUE_KEY] = $this->getContinueUrl();
        $this->google->setScopes('email');

        $_SESSION[self::ACCESS_TOKEN_KEY] = $this->google->getAccessToken();
        $loginUrl = $this->google->createAuthUrl();
        $this->redirect($loginUrl);
    }

    /**
     * {@inheritdoc}
     */
    public function setOnCodeRequest()
    {
        $this->setOnContinueUrlRequest();
        if ($code = $this->request->query->get(ThirdPartyLogin::CODE_KEY)) {
            try {
                $this->google->authenticate($code);
                if ($this->google->getAccessToken()) {
                    $tokenData = $this->google->verifyIdToken()->getAttributes();
                    $gEmail = $tokenData['payload']['email'];
                    $plus = new \Google_Service_Plus($this->google);
                    $gId = $plus->people->get('me')['id'];

                    $this->redirectWithToken($gId, $gEmail);
                }
            } catch (\Exception $e) {
                echo $e->getMessage();
            }
        } else {
            echo "Code not set.";
        }
    }
}