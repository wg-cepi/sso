<?php
namespace ModuleSSO\EndPoint\LoginMethod\ThirdParty;

use ModuleSSO\EndPoint\LoginMethod;
use ModuleSSO\JWT;

/**
 * Class ThirdPartyLogin
 * @package ModuleSSO\EndPoint\LoginMethod\ThirdParty
 */
abstract class ThirdPartyLogin extends LoginMethod
{
    /** @@var string Name of code parameter in URL */
    const CODE_KEY = 'code';

    /**
     * Redirects from endpoint URL (set in third-party application) to SSO endpoint
     *
     * @uses LoginMethod::continueUrlListener()
     * @return mixed
     */
    public abstract function codeListener();

    /**
     * Tries to find user paired with id from third-party app
     * If no user is found, method creates new user
     *
     * @param int $socialId User's id in third-party application
     * @param string $socialEmail User's email in third-party application
     *
     * @uses LoginMethod::setOrUpdateSSOCookie()
     * @uses LoginMethod::generateTokenAndRedirect()
     */
    public function redirectWithToken($socialId, $socialEmail) {
         //try to find user in facebook login pair table
        $query = \Database::$pdo->prepare("SELECT * FROM " . static::TABLE . " WHERE " . static::TABLE_COLUMN . " = '$socialId'");
        $query->execute();
        $socialUser = $query->fetch();
                
        if($socialUser) {
            //find real user
            $query = \Database::$pdo->prepare("SELECT * FROM users WHERE id = " . $socialUser['user_id']);
            $query->execute();
            $user = $query->fetch();
            if($user) {
                $this->setOrUpdateSSOCookie($user['id']);
                $this->generateTokenAndRedirect($user);
            } else {
                //social user is not bound to real user
                \Logger::log('Social user id: ' . $socialUser['id'] . ' is not bound with ' . $socialUser['user_id']);
                $data = array(
                    \ModuleSSO::METHOD_KEY => static::METHOD_NUMBER,
                    \ModuleSSO::CONTINUE_KEY => $this->getContinueUrl()
                    );
                $query = http_build_query($data);
                $this->redirect(CFG_SSO_ENDPOINT_URL . '?' .  $query);
            }
        } else {
            //no user found, let's create one
            $query = \Database::$pdo->prepare("INSERT INTO users (email) VALUES ('$socialEmail')");
            $query->execute();

            $query = \Database::$pdo->prepare("SELECT * FROM users WHERE email = '$socialEmail'");
            $query->execute();
            $user = $query->fetch();

            $query = \Database::$pdo->prepare("INSERT INTO " . static::TABLE . " (user_id, " . static::TABLE_COLUMN . ", created) VALUES (" . $user['id'] . ", '$socialId', " . time() . ")");
            $query->execute();

            $this->setOrUpdateSSOCookie($user['id']);
            $this->generateTokenAndRedirect($user);
        }
    }
}
