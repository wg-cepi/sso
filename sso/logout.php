<?php
use Symfony\Component\HttpFoundation\RedirectResponse;
session_start();
session_unset();

RedirectResponse::create(CFG_SSO_URL)->send();

