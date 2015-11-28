<?php
require_once '../libs/phpseclib1.0.0/Crypt/RSA.php';
$allowed_redirects = array("http://domain1.localhost/", "http://domain2.localhost/");
const PRIV_KEY = '-----BEGIN RSA PRIVATE KEY----- MIICXQIBAAKBgQDsHfZF9m9I5puQdyX+EEyvPv8rdz39/Ub+spB7em4DL6qglfaY /ybCwkHxvWC74dvDS/8IqF6FxLGn6Sj4hVRu7MQjOcRVc1eGbtnnLqDlHFn9byfW bJn9cToaW+43YlNAhwNSgec7LttZyULMhTFF6tmkR+5civ3gwgV6+unwkQIDAQAB AoGBALo2AvQ4HpmqrMLpBIBykFeg4hKAbtZxOd1CK+oFqt8+Z11QB3OvvfzYwLMK PFDQFcXWmGJWjn0Gm2kl25brZ6Kzl1Fi85CwCsYwJ+V56m2JjC3Lxc6LU7UV4l3q QmEoDjWeI55FnCeQ+5ME59y8s0HyRLwnerMocyrmDBuXMwsFAkEA+60vB2RWRBD/ i0RqIPbeTd1RlDDk/2Epx/CNz0ap36ahY6UtXQkMd9sHarIOhusrXp57T0HaNG9D 71dhVHe1owJBAPAsWe/KfCIO8n66mAYZ7ecSDzqvVHFqfoedMA/5XZ81Cn/Zu3qS 62bcFcLYl+Qw4an7Gs30Sn3LNPSu1cWJ3DsCQQC2reRfBzOewH/cxNIMD2UZO7ZF TKBLxmkfWbp1Y6NWVYr72x9sUm8caH2fspLc18JpMbvrsa8DNGgpSFG7kBDlAkAx jOTtPPhJSo4rKTIOKDFV9/reX6frUk5SilKNKSRwoU/OOsycKE2axhNTRL5pnNAh 8qWAEkOAGnmNdbiy7ZNbAkB4ZDLkx2/FOl3p5NJzmeWIUixa72djbCWQ+HJDOuAr ZGYCv7ghjoXKRWk65skKEIYoR81Gy/cBQoVOBUZrK5ps -----END RSA PRIVATE KEY-----';
//print_r($_COOKIE);
if(isset($_COOKIE['sso_cookie'])) {
    echo "already loged";
}
else {
     if(isset($_POST['username']) && isset($_POST['password']) && isset($_POST['redir'])) {
        if($_POST['username'] === 'awd' && $_POST['password'] === 'awd') {
            echo "succes login, generate token";
            $token = json_encode(array("id"=>10, "timestamp"=>Time()));

            $rsa = new Crypt_RSA();
            //extract($rsa->createKey());
            $rsa->loadKey(PRIV_KEY);
            $token = $rsa->encrypt($token);
            $token =  base64_encode($token);
            setcookie('sso_cookie', 1);
            
            if(in_array($_POST['redir'], $allowed_redirects)){
                echo "<script>window.parent.location = '" . $_POST['redir'] . "?" . http_build_query(array("token" =>$token)) . "';console.log(window.parent.location);</script>";
            }
            else {
                 echo "<script>console.log('ne');</script>";
            }
        }
     }
     else {
        echo '<form method="post" action="http://sso.localhost/login.php">'
        . '<label>Login:<input type="text" name="username"/></label><br/>'
        . '<label>Password:<input type="password" name="password"/></label><br/>'
        . '<input type="hidden" name="redir" value="'.$_GET['redir'].'"/>'
        . '<input type="submit" value="login"/>'
        . '</form>';
     }
}


