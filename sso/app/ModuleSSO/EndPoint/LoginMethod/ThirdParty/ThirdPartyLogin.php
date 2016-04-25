<?php
namespace ModuleSSO\EndPoint\LoginMethod\ThirdParty;

use ModuleSSO\EndPoint\LoginMethod;

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
     * @uses LoginMethod::setOnContinueUrlRequest()
     * @return mixed
     */
    public abstract function setOnCodeRequest();

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
        $query = \Database::$pdo->prepare("SELECT * FROM " . static::TABLE . " WHERE " . static::TABLE_COLUMN . " = $socialId");
        $query->execute();
        $socialUser = $query->fetch();
                
        if($socialUser) {
            //find real user
            $query = \Database::$pdo->prepare("SELECT * FROM users WHERE id = '" . $socialUser['user_id'] . "'");
            $query->execute();
            $user = $query->fetch();
            if($user) {
                $this->setOrUpdateSSOCookie($user['id']);
                $this->generateTokenAndRedirect($user);
            } else {
                //social user is not bound to real user
                //create new user if email is not used and bind him
                $query = \Database::$pdo->prepare("SELECT * FROM users WHERE email = '$socialEmail'");
                $query->execute();
                $user = $query->fetch();
                if(!$user) {
                    $query = \Database::$pdo->prepare("INSERT INTO users (id, email) VALUES (" . $socialUser['user_id'] . ", '$socialEmail')");
                    $query->execute();

                    $userId = \Database::$pdo->lastInsertId('id');
                    $userQuery = \Database::$pdo->prepare("SELECT * FROM users WHERE id=$userId");
                    $userQuery->execute();
                    $user = $userQuery->fetch();
                } else {
                    //bind to existing user
                    $query = \Database::$pdo->prepare("UPDATE " . static::TABLE . " SET user_id=" . $user['id']);
                    $query->execute();
                }

                $this->setOrUpdateSSOCookie($user['id']);
                $this->generateTokenAndRedirect($user);
            }
        } else {
            //no user found, let's create one
            $query = \Database::$pdo->prepare("SELECT * FROM users WHERE email = '$socialEmail'");
            $query->execute();
            $user = $query->fetch();

            //if user with given sso email does not exist
            if(!$user) {
                $query = \Database::$pdo->prepare("INSERT INTO users (email) VALUES ('$socialEmail')");
                $query->execute();

                $userId = \Database::$pdo->lastInsertId('id');
                $userQuery = \Database::$pdo->prepare("SELECT * FROM users WHERE id=$userId");
                $userQuery->execute();
                $user = $userQuery->fetch();
            }

            $query = \Database::$pdo->prepare("INSERT INTO " . static::TABLE . " (user_id, " . static::TABLE_COLUMN . ", created) VALUES (" . $user['id'] . ", $socialId, " . time() . ")");
            $query->execute();

            $this->setOrUpdateSSOCookie($user['id']);
            $this->generateTokenAndRedirect($user);
        }
    }
}
