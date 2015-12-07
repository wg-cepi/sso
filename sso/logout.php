<?php
session_start();
session_unset();
header("Location: http://sso.local/");
exit;

