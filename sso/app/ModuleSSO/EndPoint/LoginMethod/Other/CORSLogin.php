<?php
namespace ModuleSSO\EndPoint\LoginMethod\Other;

use ModuleSSO\EndPoint\LoginMethod;
use ModuleSSO\JWT;
use ModuleSSO\Cookie;

/**
 * Class CORSLogin
 * @package ModuleSSO\EndPoint\LoginMethod\Other
 */
class CORSLogin extends LoginMethod
{
    /**
     * @var int Number of login method
     */
    const METHOD_NUMBER = 3;

    /**
     * If email and password field are set i $_GET returns JSON with JWT, otherwise returns just JSON with status and code
     *
     * @throws \Exception
     *
     * @uses LoginMethod::setOrUpdateSSOCookie()
     * @uses JWT::generate()
     */
    public function loginListener()
    {
        if(isset($_SERVER['HTTP_ORIGIN'])) {
            header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
            header('Content-Type: application/json');
            header('Access-Control-Allow-Credentials: true');

            if (!empty($_GET['email']) && !empty($_GET['password'])) {
                $email = $_GET['email'];
                $password = $_GET['password'];

                $query = \Database::$pdo->prepare("SELECT * FROM users WHERE email = ?");
                $query->execute(array($email));
                $user = $query->fetch();
                if ($user && $this->verifyPasswordHash($password, $user['password'])) {
                    $this->setOrUpdateSSOCookie($user['id']);
                    $token = (new JWT($this->getDomain()))->generate(array('uid' => $user['id']));

                    echo '{"status":"ok","' . \ModuleSSO::TOKEN_KEY . '":"' . $token . '"}';
                } else {
                    echo json_encode(array("status" => "fail", "code" => "user_not_found"));
                }
            } else {
                echo json_encode(array("status" => "fail", "code" => "bad_login"));
            }
        } else {
            //probably won't reach this because of Same origin policy
            echo json_encode(array("status" => "fail", "code" => "http_origin_not_set"));
        }
    }

    /**
     * {@inheritdoc}
     *
     * Checks origin of request.
     * Binds loginListener or checkCookieListener if origin host is allowed
     */
    public function perform()
    {
        if(isset($_SERVER['HTTP_ORIGIN'])){
            $parsed = parse_url($_SERVER['HTTP_ORIGIN']);
            if(isset($parsed['host'])) {
                if($this->isInWhiteList($parsed['host'])) {
                    if(isset($_GET[\ModuleSSO::LOGIN_KEY]) && $_GET[\ModuleSSO::LOGIN_KEY] == 1) {
                        $this->loginListener();
                    } else if(isset($_GET[\ModuleSSO::CHECK_COOKIE_KEY]) && $_GET[\ModuleSSO::CHECK_COOKIE_KEY] == 1) {
                        $this->checkCookieListener();
                    }
                } else {
                    //domain not allowed
                    echo json_encode(array("status" => "fail", "code" => "domain_not_allowed"));
                }
            } else {
                //URL does not contain host
                echo json_encode(array("status" => "fail", "code" => "bad_continue_url"));
            }
        } else {
            //probably won't reach this because of Same origin policy
            echo json_encode(array("status" => "fail", "code" => "http_origin_not_set"));
        }
        
    }

    /**
     * Checks if SSO cookie is set and tries to get user from that cookie
     *
     * @throws \Exception
     *
     * @uses LoginMethod::getUserFromCookie()
     * @uses JWT::generate()
     */
    private function checkCookieListener()
    {
        header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
        header('Access-Control-Allow-Credentials: true');
        header('Content-Type: application/json');
        if(!isset($_COOKIE[Cookie::SECURE_SSO_COOKIE])) {
            echo json_encode(array("status" => "no_cookie"));
        } else {
            $user = $this->getUserFromCookie();
            if($user) {
                $token = (new JWT($this->getDomain()))->generate(array('uid' => $user['id']));
                echo '{"status": "ok","' . \ModuleSSO::TOKEN_KEY . '":"' . $token . '","email": "' . $user['email'] . '"}';
            } else {
                echo json_encode(array("status" => "fail", 'code' => 'bad_cookie'));
            }
        }
    }
}
