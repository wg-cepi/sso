<?php
namespace ModuleSSO\LoginMethod;

use ModuleSSO\LoginMethod;
abstract class ThirdPartyLogin extends LoginMethod {
    public function redirect($url, $code = 302)
    {
        
    }
}
