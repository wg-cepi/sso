<?php

use ModuleSSO\EndPoint\LoginMethod\Renderer\HTML\NoScriptLoginRenderer;
use ModuleSSO\EndPoint\LoginMethod\Renderer\HTML\IframeLoginRenderer;
use ModuleSSO\EndPoint\LoginMethod\Renderer\HTML\DirectLoginRenderer;

if($this instanceof DirectLoginRenderer || $this instanceof NoScriptLoginRenderer) $html .= $this->appendHeader();
$class = isset($params['cssClass']) ? 'class="' . $params['cssClass'] . '"' : '';
$html .= '<div id="id-sso-links" ' . $class . '>';
    $html .= '<p>You are logged in as <strong>' . $params['user']['email'] . '</strong> at <a href="' . CFG_SSO_ENDPOINT_URL . '">Webgarden SSO</a></p>';
        $html .= '<ul>';

        if($this instanceof NoScriptLoginRenderer || $this instanceof IframeLoginRenderer) {
            if(!isset($params['continueUrl'])) throw new \Exception('Param "continueUrl" not set');
            if(!isset($params['methodNumber'])) throw new \Exception('Param "methodNumber" not set');
            if(!isset($params['user'])) throw new \Exception('Param "user" not set');

            if ($params['continueUrl'] !== CFG_SSO_ENDPOINT_URL) {
                $data = array(
                    \ModuleSSO::METHOD_KEY => $params['methodNumber'],
                    \ModuleSSO::LOGIN_KEY => 1,
                    \ModuleSSO::CONTINUE_KEY => $params['continueUrl']
                );
                $query = http_build_query($data);
                $src = CFG_SSO_ENDPOINT_URL . '?' . $query;
                $html .= '<li><a href="' . $src . '" title="Continue as ' . $params['user']['email'] . '"> Continue as ' . $params['user']['email'] . '</a></li>';
            }

            $data = array(
                \ModuleSSO::METHOD_KEY => $params['methodNumber'],
                \ModuleSSO::RELOG_KEY => 1,
                \ModuleSSO::CONTINUE_KEY => $params['continueUrl']
            );
            $query = http_build_query($data);
            $src = CFG_SSO_ENDPOINT_URL . '?' . $query;
            $html .= '<li><a href="' . $src . '" title="Log in as another user">Log in as another user</a></li>';
        }
        if($this instanceof DirectLoginRenderer) {
            $html .= '<li><a href="' . CFG_SSO_ENDPOINT_URL . '?' . \ModuleSSO::RELOG_KEY . '=1" title="Log in as another user">Log in as another user to Webgarden SSO</a></li>';
            $html .= '<li><a href="?' . \ModuleSSO::LOGOUT_KEY. '=1" title="Logout">Logout from Webgarden SSO</a></li>';
        }
    $html .= '</ul>';
$html .= '</div>';