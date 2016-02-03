<?php
namespace ModuleSSO\EndPoint\LoginMethod\ThirdParty;

class GoogleLogin extends ThirdPartyLogin {
    
    const METHOD_NUMBER = 5;
    const TABLE = 'user_login_google';
    const TABLE_COLUMN = 'google_id';
    
    private $google;
    
    public function __construct()
    {
        $this->google = new \Google_Client();
        $this->google->setClientId(CFG_G_CLIENT_ID);
        $this->google->setClientSecret(CFG_G_CLIENT_SECRET);
        $this->google->setRedirectUri(CFG_G_REDIRECT_URI);
    }
    
    public function loginListener()
    {
        $_SESSION['continueUrl'] = $this->getContinueUrl();
        $this->google->setScopes('email');
        
        $_SESSION['google_access_token'] = $this->google->getAccessToken();
        $loginUrl = $this->google->createAuthUrl();
        $this->redirect($loginUrl);
    }
    
    public function redirectAndLogin()
    {
        $url = isset($_SESSION['continueUrl']) ? $_SESSION['continueUrl'] : CFG_SSO_ENDPOINT_URL;
        $continueUrl = $this->getContinueUrl($url);
        if(isset($_GET['code'])) {
            try {
                $this->google->authenticate($_GET['code']);
                //$_SESSION['google_access_token'] = $this->google->getAccessToken();
                //$this->google->setAccessToken($_SESSION['google_access_token']);
                if ($this->google->getAccessToken()) {
                    //$_SESSION['google_access_token'] = $this->google->getAccessToken();
                    $tokenData = $this->google->verifyIdToken()->getAttributes();
                    $gEmail = $tokenData['payload']['email'];
                    $plus = new \Google_Service_Plus($this->google);
                    $gId = $plus->people->get('me')['id'];
                    
                    $this->redirectWithToken($gId, $gEmail, $continueUrl);
                }
            } catch (\Exception $e) {
                echo $e->getMessage();
            }
        } else {
            echo "Code not set.";
        }

        
    }
}