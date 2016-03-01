# Webgarden SSO
## Setup
Works for Linux as well, you will need LAMP or PHP + MySQL + Apache
1. Edit hosts file and add following entry
    ```text
    127.0.0.1 domain1.local domain2.local sso.local
    ```
2. Install WAMP
    * assume, that you have installed WAMP to `C:/wamp`
    * you can create virtual domain in separated files in `C:/wamp/vhosts folder` and include them in `httpd.conf` with `IncludeOptional "C:/wamp/vhosts/*"`
3. Create virtual domains in `httpd.conf` or `vhosts` folder
    ```text
    <VirtualHost *:80>
      DocumentRoot "C:/wamp/www/sso/domain2"
      ServerName domain2.local
      ServerAlias *.domain2.local domain2.local
    </VirtualHost>

    <VirtualHost *:80>
      DocumentRoot "C:/wamp/www/sso/domain1"
      ServerName domain1.local
      ServerAlias *.domain1.local domain1.local
    </VirtualHost>

    <VirtualHost *:80>
      DocumentRoot "C:/wamp/www/sso/sso"
      ServerName sso.local
    </VirtualHost>
    ```
4. Create a file `googleLogin.php` in `C:/wamp/www` with following contents which will redirect response from Google API to Webgarden SSO Endpoint
    ```php
    <?php
    //C:/wamp/www/googleLogin.php
    if(isset($_GET['code'])) {
    	$code = $_GET['code'];
    	header("Location: http://sso.local/googleLogin.php?code=" . $code);
    	exit;
    }
     ```