<?php

/**
 * Created by PhpStorm.
 * User: yadmin
 * Date: 03.02.2016
 * Time: 18:21
 */
class JWTTest extends PHPUnit_Framework_TestCase
{
    private $publicKey;
    public function setUp()
    {
        \Database::init();
        \ModuleSSO\BrowserSniffer::init();
        $this->publicKey = file_get_contents('app/../../domain1/app/config/pk.pub');
    }
    public function testGenerateSimpleConstructor()
    {
        $jwt = new \ModuleSSO\JWT('test.local');
        $token = $jwt->generate(array('uid' => 1));

        $token = (new \Lcobucci\JWT\Parser())->parse((string) $token);
        $signer = new \Lcobucci\JWT\Signer\Rsa\Sha256();
        $keychain = new \Lcobucci\JWT\Signer\Keychain();

        $this->assertEquals($token->verify($signer, $keychain->getPublicKey($this->publicKey)), true);
        $this->assertEquals($token->getClaim('uid'), 1);
        $this->assertEquals($token->getClaim('iss'), 'sso.local');

    }

    public function testGenerateFullConstructor()
    {
        $jwt = new \ModuleSSO\JWT('test.local', 'issuer.local');
        $token = $jwt->generate(array('uid' => 1));

        $token = (new \Lcobucci\JWT\Parser())->parse((string) $token);
        $signer = new \Lcobucci\JWT\Signer\Rsa\Sha256();
        $keychain = new \Lcobucci\JWT\Signer\Keychain();

        $this->assertEquals($token->verify($signer, $keychain->getPublicKey($this->publicKey)), true);
        $this->assertEquals($token->getClaim('uid'), 1);
        $this->assertEquals($token->getClaim('iss'), 'issuer.local');
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Bad or empty argument
     */
    public function testGenerateBadParam()
    {
        $jwt = new \ModuleSSO\JWT('test.local');
        $token = $jwt->generate('BAD!PARAM!HERE');
    }
}
