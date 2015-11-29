<?php

echo "<script src='http://code.jquery.com/jquery-2.1.4.min.js'></script>";
echo "<script src='app/js/cors.js'></script>";
echo "<h1>Domain 2</h1>";

echo '<div id="loginArea">';
echo '<form>'
    . '<label>Email:<input type="text" name="email"/></label><br/>'
    . '<label>Password:<input type="password" name="password"/></label><br/>'
    . '<input type="hidden" name="continue" value="http://domain2.local/cors.php"/>'
    . '<input type="button" id="loginButton" value="login"/>'
. '</form>';
echo '</div>';


