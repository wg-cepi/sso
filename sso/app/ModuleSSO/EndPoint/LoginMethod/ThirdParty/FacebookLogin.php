<?php
namespace ModuleSSO\EndPoint\LoginMethod\ThirdParty;

use Facebook\Facebook;
use Facebook\Exceptions;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class FacebookLogin
 * @package ModuleSSO\EndPoint\LoginMethod\ThirdParty
 */
class FacebookLogin extends ThirdPartyLogin
{
    /**
     * @var int Number of method
     */
    const METHOD_NUMBER = 4;

    /**
     * @var string Name of social pairing table
     */
    const TABLE = 'user_login_facebook';

    /**
     * @var string Name of column in social pairing table
     */
    const TABLE_COLUMN = 'facebook_id';

    /**
     * @var Facebook
     */
    private $facebook;
    /**
     * @var \Facebook\Helpers\FacebookRedirectLoginHelper
     */
    private $helper;

    /**
     * FacebookLogin constructor.
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->facebook = new Facebook([
            'app_id' => CFG_FB_APP_ID,
            'app_secret' => CFG_FB_APP_SECRET,
            'default_graph_version' => 'v2.2',
            ]);

        $this->helper = $this->facebook->getRedirectLoginHelper();
    }
    /**
     * {@inheritdoc}
     */
    public function setOnLoginRequest()
    {
        $_SESSION[\ModuleSSO::CONTINUE_KEY] = $this->getContinueUrl();
        $permissions = ['email'];
        $loginUrl = $this->helper->getLoginUrl(CFG_FB_LOGIN_ENDPOINT . '?' . \ModuleSSO::CONTINUE_KEY . '=' . $this->getContinueUrl(), $permissions);
        $this->redirect($loginUrl);
    }

    /**
     * {@inheritdoc}
     */
    public function setOnCodeRequest()
    {
        $this->setOnContinueUrlRequest();
        try {
            $accessToken = $this->helper->getAccessToken();
            $this->facebook->setDefaultAccessToken((string)$accessToken);

            try {
                $response = $this->facebook->get('/me?fields=email');
                $userNode = $response->getGraphUser();
                $fbId = $userNode->getId();
                $fbEMail = $userNode->getEmail();
              
               $this->redirectWithToken($fbId, $fbEMail);
                
            } catch(Exceptions\FacebookResponseException $e) {
                // When Graph returns an error
                echo 'Graph returned an error: ' . $e->getMessage();
                exit;
            } catch(Exceptions\FacebookSDKException $e) {
                // When validation fails or other local issues
                echo 'Facebook SDK returned an error: ' . $e->getMessage();
                exit;
            }
        } catch(Exceptions\FacebookResponseException $e) {
            // When Graph returns an error
            echo 'Graph returned an error: ' . $e->getMessage();
            exit;
        } catch(Exceptions\FacebookSDKException $e) {
            // When validation fails or other local issues
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }
    }
}