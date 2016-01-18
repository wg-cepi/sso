<?php
namespace ModuleSSO\LoginMethod\ThirdPartyLogin;

use ModuleSSO\LoginMethod\ThirdPartyLogin;

class FacebookLogin extends ThirdPartyLogin {
    
    const METHOD_NUMBER = 4;
    private $facebook;
    
    public function __construct()
    {
       $this->facebook = new \Facebook\Facebook([
            'app_id' => CFG_FB_APP_ID,
            'app_secret' => CFG_FB_APP_SECRET,
            'default_graph_version' => 'v2.2',
            ]);
       
       $this->helper = $this->facebook->getRedirectLoginHelper();
    }
    public function showClientLogin()
    {
        $permissions = ['email'];
        $loginUrl = $this->helper->getLoginUrl(CFG_SSO_ENDPOINT_URL . '?' . \ModuleSSO::METHOD_KEY . '=' . self::METHOD_NUMBER . '&continue=' . CFG_DOMAIN_URL, $permissions);
        return '<a href="' . $loginUrl . '">Log in with Facebook</a>';
    }
    
    public function login()
    {
        $continueUrl = $this->getContinueUrl();
        try {
            $accessToken =$this->helper->getAccessToken();
            $this->facebook->setDefaultAccessToken($accessToken);

            try {
              $response = $this->facebook->get('/me?fields=email');
              $userNode = $response->getGraphUser();
              $userNode->getId();
              
              //try to find user in facebook login pair table
              
              //token
              //$this->redirect($continueUrl);
              echo $continueUrl;
              
              
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
    

}