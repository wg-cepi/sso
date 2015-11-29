<?php
session_start();
unset($_SESSION["uid"]);
header("Location: http://domain1.local/");

