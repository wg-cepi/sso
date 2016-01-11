<?php

class AntiCSRF
{
    public static function check($token)
    {       
        $query = Database::$pdo->prepare("SELECT * FROM login_request WHERE code = '$token' AND completed = 0 AND expires > " . time());
        $query->execute();
        $request = $query->fetch();
        
        if(!empty($request['code']) && $request['code'] === $token && $request['expires'] > time()) {            
            $query = Database::$pdo->prepare("UPDATE login_request SET completed = 1 WHERE code = '$token'");
            $query->execute();
            return true;
        } else {
            return false;
        }

        // Origin checks
        /*
         * extra checks todo
         * $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT']
         */
    }

    public static function generate($domainName)
    {
        $query = Database::$pdo->prepare("SELECT * FROM domains WHERE name = '$domainName'");
        $query->execute();
        $domain = $query->fetch();
        if ($domain) {
            // extra ...$_SERVER['REMOTE_ADDR'] , $_SERVER['HTTP_USER_AGENT'] ;
            $token = base64_encode(self::randomString( 32 ) );
            $query = Database::$pdo->prepare("INSERT INTO login_request (domain_id, code, expires, completed) VALUES (?, ?, ?, ?)");
            $query->execute(array($domain['id'], $token, time() + 3600, 0));
            return $token;
        } else {
            throw new Exception("adwadw");
        }
        
    }

    protected static function randomString( $length )
    {
        $seed = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijqlmnopqrtsuvwxyz0123456789';
        $max = strlen( $seed ) - 1;
        $string = '';
        for ( $i = 0; $i < $length; ++$i )
            $string .= $seed[intval( mt_rand( 0.0, $max ))];
        return $string;
    }
}