<?php
namespace ModuleSSO\EndPoint\LoginMethod\HTTP;

use ModuleSSO\EndPoint\LoginMethod;
use ModuleSSO\Messages;

/**
 * Class HTTPLogin
 * @package ModuleSSO\EndPoint\LoginMethod\HTTP
 */
abstract class HTTPLogin extends LoginMethod
{
    /**
     * Listens for $_GET parameters and performs appropriate commands
     *
     * If email and password are set in $_GET, method creates SSO cookie and redirects user with generated token
     * If continue key is set in $_GET, method updates SSO cookie and redirects user with generated token
     * If relog key is set in $_GET, method shows login form
     * If none of conditions mentioned above is met, method checks if SSO cookie is set and tries to obtain user, otherwise method shows login form
     *
     * @uses \ModuleSSO::LOGIN_KEY
     * @uses \ModuleSSO::RELOG_KEY
     * @uses \ModuleSSO\Cookie::SECURE_SSO_COOKIE
     */
    public function setOnLoginRequest()
    {
        if($this->request->get('email') && $this->request->get('password')) {
            $email = $this->request->get('email');
            $password = $this->request->get('password');

            $query = \Database::$pdo->prepare("SELECT * FROM users WHERE email = ?");
            $query->execute(array($email));
            $user = $query->fetch();
            if($user && \ModuleSSO::verifyPasswordHash($password, $user['password'])) {
                $this->setOrUpdateSSOCookie($user['id']);
                $this->generateTokenAndRedirect($user);
            } else {
                Messages::insert('Login failed, please try again', 'warn');
                $this->showLoginForm();
            }
        } else if($this->request->get(\ModuleSSO::LOGIN_KEY)) {
            if($user = $this->getUserFromCookie()) {
                $this->generateTokenAndRedirect($user);
            } else {
                Messages::insert('Login failed, please try again', 'warn');
                $this->showLoginForm();
            }
        }
        else if ($this->request->get(\ModuleSSO::RELOG_KEY)){
            $this->showLoginForm();
        }
        else {
            $this->showHTML();
        }
    }

    /**
     * Generates HTML login form for NoScript login and Iframe login
     */
    public function showLoginForm()
    {
        $params = array(
            'methodNumber' => static::METHOD_NUMBER,
            'continueUrl' => $this->continueUrl,
            'cssClass' => \ModuleSSO::METHOD_KEY .  static::METHOD_NUMBER . "-" . str_replace('.', '-' , $this->getDomain())
        );
        $this->renderer->renderLoginForm($params);
    }

    /**
     * Generates user information in HTML
     *
     * @param $user
     * @return string HTML containing info about user
     */
    public function showContinueOrRelog($user)
    {
        $params = array(
            'user' => $user,
            'methodNumber' => static::METHOD_NUMBER,
            'continueUrl' => $this->continueUrl,
            'cssClass' => \ModuleSSO::METHOD_KEY .  static::METHOD_NUMBER . "-" . str_replace('.', '-' , $this->getDomain())
        );
        $this->renderer->renderContinueOrRelog($params);
    }

    /**
     * If user exists, method shows user info, otherwise shows login form
     *
     * @return string HTML string
     *
     * @uses LoginMethod::showContinueOrRelog()
     * @uses LoginMethod::showLoginForm()
     */
    public function showHTML()
    {
        $user = $this->getUserFromCookie();
        if($user !== null) {
            $this->showContinueOrRelog($user);
        } else {
            $this->showLoginForm();
        }
    }
}

