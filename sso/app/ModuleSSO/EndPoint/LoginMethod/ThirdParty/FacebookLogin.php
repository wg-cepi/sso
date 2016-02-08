<?php
namespace ModuleSSO\EndPoint\LoginMethod\ThirdParty;

use Facebook\Facebook;
use Facebook\Exceptions;

class FacebookLogin extends ThirdPartyLogin {
    
    const METHOD_NUMBER = 4;
    const TABLE = 'user_login_facebook';
    const TABLE_COLUMN = 'facebook_id';
    
    private $facebook;
    private $helper;
    
    public function __construct()
    {
       $this->facebook = new Facebook([
            'app_id' => CFG_FB_APP_ID,
            'app_secret' => CFG_FB_APP_SECRET,
            'default_graph_version' => 'v2.2',
            ]);
       
       $this->helper = $this->facebook->getRedirectLoginHelper();
    }
    
    public function redirectAndLogin()
    {
        $this->continueUrlListener();
        try {
            $accessToken =$this->helper->getAccessToken();
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
    
    
    public function loginListener()
    {
        $_SESSION[\ModuleSSO::CONTINUE_KEY] = $this->getContinueUrl();
        $permissions = ['email'];
        $loginUrl = $this->helper->getLoginUrl(CFG_FB_LOGIN_ENDPOINT . '?' . \ModuleSSO::CONTINUE_KEY . '=' . $this->getContinueUrl(), $permissions);
        $this->redirect($loginUrl);
    }
}