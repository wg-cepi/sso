<?php

use \Lcobucci\JWT\Parser;
use \Lcobucci\JWT\Signer\Rsa\Sha256;
use \Lcobucci\JWT\Signer\Key;

class JWTTest extends PHPUnit_Framework_TestCase
{
    private $publicKey;
    public function setUp()
    {
        \Database::init();
        \ModuleSSO\BrowserSniffer::init();
        $this->publicKey = file_get_contents(PROJECT_ROOT .'/domain1/app/config/pk.pub');
    }
    public function testGenerateSimpleConstructor()
    {
        $jwt = new \ModuleSSO\JWT('test.local');
        $token = $jwt->generate(array('uid' => 1));

        $token = (new Parser())->parse((string) $token);
        $signer = new Sha256();
        $pubKey =  new Key($this->publicKey);

        $this->assertEquals($token->verify($signer, $pubKey), true);
        $this->assertEquals($token->getClaim('uid'), 1);
        $this->assertEquals($token->getClaim('iss'), 'sso.local');

    }

    public function testGenerateFullConstructor()
    {
        $notBefore = time() + 10;
        $issuedAt = time();
        $exp = time() + 60;
        $jwt = new \ModuleSSO\JWT('test.local', 'issuer.local', $exp, $issuedAt, $notBefore, PROJECT_ROOT  .'/sso/app/config/pk.pem');
        $token = $jwt->generate(array('uid' => 1));

        $token = (new Parser())->parse((string) $token);
        $signer = new Sha256();
        $pubKey =  new Key($this->publicKey);

        $this->assertEquals($token->verify($signer, $pubKey), true);
        $this->assertEquals($token->getClaim('uid'), 1);
        $this->assertEquals($token->getClaim('iss'), 'issuer.local');
        $this->assertEquals($token->getClaim('exp'), $exp);
        $this->assertEquals($token->getClaim('exp'), $exp);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Bad or empty argument
     */
    public function testGenerateBadParam()
    {
        $jwt = new \ModuleSSO\JWT('test.local');
        $jwt->generate('BAD!PARAM!HERE');
    }
}
