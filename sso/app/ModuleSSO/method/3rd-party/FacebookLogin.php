<?php
namespace ModuleSSO\LoginMethod\ThirdPartyLogin;

use ModuleSSO\LoginMethod\ThirdPartyLogin;
use ModuleSSO\JWT;

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
        //$permissions = ['email'];
        //$loginUrl = $this->helper->getLoginUrl(CFG_FB_REDIR_URL . '?' . \ModuleSSO::CONTINUE_KEY . '=' . CFG_DOMAIN_URL, $permissions);
        return '<a href="' . CFG_SSO_ENDPOINT_URL . '?' . \ModuleSSO::CONTINUE_KEY . '=' . CFG_DOMAIN_URL . '&' . \ModuleSSO::METHOD_KEY . '=' . self::METHOD_NUMBER . '">Log in with Facebook</a>';
    }
    
    public function rediretWithToken()
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
              
                //try to find user in facebook login pair table
                $query = \Database::$pdo->prepare("SELECT * FROM user_login_facebook WHERE facebook_id = $fbId");
                $query->execute();
                $fbUser = $query->fetch();
                if($fbUser) {
                    //find real user
                    $query = \Database::$pdo->prepare("SELECT * FROM users WHERE id = " . $fbUser['user_id']);
                    $query->execute();
                    $user = $query->fetch();
                    if($user) {
                        $this->setCookies($user['id']);
                        $token = (new JWT($this->domain))->generate(array('uid' => $user['id']));

                        $query = \ModuleSSO::TOKEN_KEY . '=' . $token; 
                        $url = $continueUrl .  "?" . $query;
                        $this->redirect($url);
                    } else {
                        $data = array(
                            \ModuleSSO::METHOD_KEY => self::METHOD_NUMBER,
                            \ModuleSSO::CONTINUE_KEY => $continueUrl
                            );
                        $query = http_build_query($data);
                        $this->redirect(CFG_SSO_ENDPOINT_URL . '?' .  $query);
                    }
                } else {
                    //no user found, let's create one
                    $query = \Database::$pdo->prepare("INSERT INTO users (email) VALUES ('$fbEMail')");
                    $query->execute();

                    $query = \Database::$pdo->prepare("SELECT * FROM users WHERE email = '$fbEMail'");
                    $query->execute();
                    $user = $query->fetch();

                    $query = \Database::$pdo->prepare("INSERT INTO user_login_facebook (user_id, facebook_id, created) VALUES (" . $user['id'] . ", $fbId, " . time() . ")");
                    $query->execute();

                    $this->setCookies($user['id']);

                    $token = (new JWT($this->domain))->generate(array('uid' => $user['id']));
                    $url = $continueUrl .  "?" . \ModuleSSO::TOKEN_KEY . "=" . $token;
                    $this->redirect($url);
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
    
    public function showSSOLogin()
    {
        $continueUrl = $this->getContinueUrl();
        $_SESSION['continueUrl'] = $continueUrl;
        $permissions = ['email'];
        $loginUrl = $this->helper->getLoginUrl(CFG_FB_LOGIN_ENDPOINT . '?' . \ModuleSSO::CONTINUE_KEY . '=' . $continueUrl, $permissions);
        return '<a href="' . $loginUrl . '">Log in with Facebook</a>';
    }
    
    public function run()
    {
        echo $this->showSSOLogin();
    }
    
    public function login()
    {
        
    }
}