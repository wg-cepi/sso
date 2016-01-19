<?php
session_start();
require_once 'Autoloader.php';

use ModuleSSO\LoginMethod\ThirdPartyLogin\FacebookLogin;
$loginMethod = new FacebookLogin();
$loginMethod->continueToSSO();

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

