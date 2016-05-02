<?php
namespace ModuleSSO;

use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Signer\Rsa\Sha256;

/**
 * Class EJWTException
 * @package ModuleSSO
 */
class EJWTException extends \Exception {}

/**
 * Class JWT
 * @package ModuleSSO
 */
class JWT
{
    /**
     * @var null|string
     */
    public $issuer = null;

    /**
     * @var string|null
     */
    public $audience = null;

    /**
     * @var int|null
     */
    public $expiration = null;

    /**
     * @var int|null
     */
    public $notBefore = null;

    /**
     * @var int|null
     */
    public $issuedAt = null;

    /**
     * @var string|null
     */
    public $token = null;

    /**
     * @var null|string
     */
    private $privateKey = null;
    /**
     * @var null|string
     */
    private $domain = null;

    /**
     * JWT constructor
     * @param string $domain
     * @param string $issuer
     * @param null $expiration
     * @param null $issuedAt
     * @param null $notBefore
     * @param null $privKeyPath
     */
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
     *
     * @throws \Exception
     * @param array $values
     *
     * @return string $token
     */
    public function generate($values)
    {
        if(!is_array($values) || empty($values)) {
            throw new EJWTException('Bad or empty argument');
        }

        $signer = new Sha256();
        $pk =  new Key($this->privateKey);
        
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
        
        $token = $builder->sign($signer, $pk)
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