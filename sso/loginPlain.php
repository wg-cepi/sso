<?php
/**
 * Endpoint for AJAX login requests
 */
session_start();
require_once 'Autoloader.php';

use ModuleSSO\EndPoint;
use ModuleSSO\BrowserSniffer;
use ModuleSSO\EndPoint\LoginMethod\Other\CORSLogin;
use Symfony\Component\HttpFoundation\Request;
use ModuleSSO\EndPoint\LoginMethod\Renderer\HTML\HTMLRenderer;

BrowserSniffer::init();
Database::init();

$request = Request::createFromGlobals();
$renderer = new HTMLRenderer();

$endPoint = new EndPoint($request, $renderer);
$endPoint->setLoginMethod(new CORSLogin($request));
$endPoint->run();