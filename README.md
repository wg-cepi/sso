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

3. Add virtual domains to Apache `httpd.conf` from [vhosts dump](dumps/vhosts.txt)
    * `httpd.conf` is located in `C:\wamp\bin\apache\apacheX.Y.Z` folder

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

5. In [sso/app/config.php](sso/app/config.php) configure
    * `CFG_SQL_HOST`
    * `CFG_SQL_DBNAME`
    * `CFG_SQL_USERNAME`
    * `CFG_SQL_PASSWORD`

6. Create database (use name in `CFG_SQL_DBNAME`) and import [SQL dump](dumps/sso.sql)

6. Access http://sso.local. You should see Webgarden SSO endpoint.
7. You have 2 pre-created users
    1. Email: joe@example.com, pass: joe
    2. Email: bob@example.com, pass bob

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
7. You WON'T see **"Continue as ..."**, because you have logged yourself out from SSO