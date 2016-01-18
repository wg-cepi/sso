<?php
namespace ModuleSSO;

use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Keychain;
use Lcobucci\JWT\Signer\Rsa\Sha256;

class JWT
{
    public $issuer = null;
    public $audience = null;
    public $expiration = null;
    public $notBefore = null;
    public $issuedAt = null;
    
    public $token = null;
    
    private $privateKey = null;
    private $domain = null;
    
    public function __construct($domain = CFG_JWT_ISSUER, $issuer = CFG_JWT_ISSUER, $expiration = null, $issuedAt = null, $notBefore = null, $privKeyPath = null)
    {
        $this->privateKey = $privKeyPath ? file_get_contents($privKeyPath) : file_get_contents(__DIR__ . '/../config/pk.pem');
        $this->issuer = $issuer;
        $this->domain = $domain;
       
        $this->expiration = $expiration ? $expiration : time() + 60;
        $this->issuedAt = $issuedAt ? $issuedAt : time();
        $this->notBefore = $notBefore ? $notBefore : time();       
    }
    
    /**
     * Generates and returns JWT based on input values and config variables
     * @param array $values
     * @return string $token
     */
    public function generate($values)
    {
        $signer = new Sha256();
        $keychain = new Keychain();
        
        $builder = new Builder();
        if($this->issuer !== null) {
            $builder->setIssuer($this->issuer);
        }
        if($this->audience !== null) {
            $builder->setAudience($this->audience);
        }
        if($this->notBefore !== null) {
            $builder->setNotBefore($this->notBefore);
        }
        if($this->issuedAt !== null) {
            $builder->setIssuedAt($this->issuedAt);
        }
        if($this->expiration !== null) {
            $builder->setExpiration($this->expiration);
        }
        
        foreach ($values as $name => $value)
        {
            $builder->set($name, $value);
        }
        
        $token = $builder->sign($signer, $keychain->getPrivateKey($this->privateKey))
                ->getToken();

        $this->token = $token;
        
        //update database tables
        $query = \Database::$pdo->prepare("SELECT * FROM domains WHERE name = '$this->domain'");
        $query->execute();
        $domain = $query->fetch();
        if($domain) {
            $query = \Database::$pdo->prepare("INSERT INTO tokens (domain_id, value, used, expires) VALUES (" . $domain['id'] . ", '$token', 0, $this->expiration)");
            $query->execute();
        }
        
        return $token;
    }
}