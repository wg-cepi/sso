<?php
namespace ModuleSSO\EndPoint\LoginMethod\ThirdParty;

class GoogleLogin extends ThirdPartyLogin {
    
    const METHOD_NUMBER = 5;
    const TABLE = 'user_login_google';
    const TABLE_COLUMN = 'google_id';
    const ACCESS_TOKEN_KEY = 'google_access_token';
    
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
        $_SESSION[\ModuleSSO::CONTINUE_KEY] = $this->getContinueUrl();
        $this->google->setScopes('email');

        $_SESSION[self::ACCESS_TOKEN_KEY] = $this->google->getAccessToken();
        $loginUrl = $this->google->createAuthUrl();
        $this->redirect($loginUrl);
    }
    
    public function redirectAndLogin()
    {
        $this->continueUrlListener();
        if(isset($_GET['code'])) {
            try {
                $this->google->authenticate($_GET['code']);
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