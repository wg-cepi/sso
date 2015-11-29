<?php
session_start();
unset($_SESSION["uid"]);
header("Location: http://domain2.local/");

