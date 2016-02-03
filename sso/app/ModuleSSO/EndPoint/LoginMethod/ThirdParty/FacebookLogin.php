<?php
namespace ModuleSSO\EndPoint\LoginMethod\ThirdParty;

class FacebookLogin extends ThirdPartyLogin {
    
    const METHOD_NUMBER = 4;
    const TABLE = 'user_login_facebook';
    const TABLE_COLUMN = 'facebook_id';
    
    private $facebook;
    private $helper;
    
    public function __construct()
    {
       $this->facebook = new \Facebook\Facebook([
            'app_id' => CFG_FB_APP_ID,
            'app_secret' => CFG_FB_APP_SECRET,
            'default_graph_version' => 'v2.2',
            ]);
       
       $this->helper = $this->facebook->getRedirectLoginHelper();
    }
    
    public function redirectAndLogin()
    {
        $url = isset($_SESSION['continueUrl']) ? $_SESSION['continueUrl'] : CFG_SSO_ENDPOINT_URL;
        $continueUrl = $this->getContinueUrl($url);
        try {
            $accessToken =$this->helper->getAccessToken();
            $this->facebook->setDefaultAccessToken((string)$accessToken);

            try {
                $response = $this->facebook->get('/me?fields=email');
                $userNode = $response->getGraphUser();
                $fbId = $userNode->getId();
                $fbEMail = $userNode->getEmail();
              
               $this->redirectWithToken($fbId, $fbEMail, $continueUrl);
                
            } catch(\Facebook\Exceptions\FacebookResponseException $e) {
                // When Graph returns an error
                echo 'Graph returned an error: ' . $e->getMessage();
                exit;
            } catch(\Facebook\Exceptions\FacebookSDKException $e) {
                // When validation fails or other local issues
                echo 'Facebook SDK returned an error: ' . $e->getMessage();
                exit;
            }
        } catch(\Facebook\Exceptions\FacebookResponseException $e) {
            // When Graph returns an error
            echo 'Graph returned an error: ' . $e->getMessage();
            exit;
        } catch(\Facebook\Exceptions\FacebookSDKException $e) {
            // When validation fails or other local issues
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }
    }
    
    
    public function loginListener()
    {
        $continueUrl = $this->getContinueUrl();
        $_SESSION['continueUrl'] = $continueUrl;
        $permissions = ['email'];
        $loginUrl = $this->helper->getLoginUrl(CFG_FB_LOGIN_ENDPOINT . '?' . \ModuleSSO::CONTINUE_KEY . '=' . $continueUrl, $permissions);
        $this->redirect($loginUrl);
    }
}