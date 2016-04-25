# Webgarden SSO
## Setup
Also works on Linux, you will need LAMP or PHP + MySQL + Apache.

1. Edit hosts file and add following entry

    ```text
    127.0.0.1 domain1.local domain2.local sso.local
    ```
    
2. Install WAMP
    * assume, that you have installed WAMP to `C:/wamp`
    * you can create virtual domains (each in separated file) in `C:/wamp/vhosts` folder and include them in `httpd.conf` with `IncludeOptional "C:/wamp/vhosts/*"`

3. Modify virtual domains in `httpd.conf` or create them in `vhosts` folder

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

4. Create a file `googleLogin.php` in `C:/wamp/www` with following contents. This will redirect response from Google API to Webgarden SSO Endpoint

    ```php
    <?php
    //C:/wamp/www/googleLogin.php
    if(isset($_GET['code'])) {
    	$code = $_GET['code'];
    	header("Location: http://sso.local/googleLogin.php?code=" . $code);
    	exit;
    }
    ```

5. In `sso/config.php` configure
    * `CFG_SQL_HOST`
    * `CFG_SQL_DBNAME`
    * `CFG_SQL_USERNAME`
    * `CFG_SQL_PASSWORD`

6. Create database (use name in `CFG_SQL_DBNAME`) and import SQL dump [Read more words!](dumps/sso.sql)

6. Access http://sso.local. You should see Webgarden SSO endpoint.
7. You have 3 pre-created users
    1. Email: joe@example.com, pass: joe
    2. Email: bob@example.com, pass bob
    3. User for FB or Google login
        * email: testsso@wgz.cz
        * google pass: test1234//
        * facebook pass: test1234

## Playing with SSO
### Login scenario
1. Access http://domain1.local and login
    * Email: joe@example.com
    * Password: joe
2. Access http://domain2.local
    1. You will see **"Continue as joe@example.com"**
    2. Click **"Continue as ..."**
    3. Now you are logged in as joe@example.com
        * You are on different domain
        * You did not have to enter your credentials again

### Local logout scenario
1. Login http://domain1.local
2. Press **"Local logout"** button
3. Go to http://domain2.local
4. Login at http://domain2.local
5. Now you are logged in at http://domain2.local
    * You are not logged in at http://domain1.local but you have logged in there
    
### Global logout scenario
1. Login http://domain1.local
2. Navigate to http://domain2.local
3. You will see **"Continue as ..."**
4. Navigate to http://domain1.local
5. Press **"Global logout"** button
6. Navigate to http://domain2.local
7. You WON'T see **"Continue as ..."**, because you have logged yourself from SSO