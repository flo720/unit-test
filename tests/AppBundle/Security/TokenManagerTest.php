<?php

namespace Tests\AppBundle\Security;

use AppBundle\Entity\User;
use AppBundle\Security\TokenManager;
use Lcobucci\JWT\Token;
use Psr\Log\LoggerInterface;
use Tests\AppBundle\Security\Mocker\AbstractTokenManagerProviderTest;

/**
 * Class TokenManagerTest
 * @package AppBundle\Tests\Security
 */
class TokenManagerTest extends AbstractTokenManagerProviderTest
{
    /**
     * @var TokenManager
     */
    private $tokenManager;

    /**
     * @var TokenManager
     */
    private $invalidTokenManager;

    /**
     * @var User
     */
    private $user;

    /**
     * Test init TokenManagerTest
     */
    public function setUp()
    {
        $this->user = (new User())->setId(1)
            ->setPassword('8c6976e5b5410415bde908bd4dee15dfb167a9c873fc4bb8a81f6f2ab448a918')
            ->setUsername('test');

        /** @var LoggerInterface $logger */
        $logger = $this->getMockLogger();

        $this->tokenManager = new TokenManager(
            $logger,
            'test'
        );

        $this->invalidTokenManager = new TokenManager(
            $logger,
            'test2'
        );
    }

    /**
     * testCreateToken
     */
    function testCreateToken()
    {
        $this->assertInstanceOf(Token::class, $this->tokenManager->createToken($this->user));
    }

    /**
     * testValidParseTokenString
     */
    function testValidParseTokenString()
    {
        $validTokenString = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9'
            .'.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiYWRtaW4iOnRydWV9'
            .'.TJVA95OrM7E2cBab30RMHrHDcEfxjoYZgeFONFh7HgQ';

        $this->assertInstanceOf(Token::class, $this->tokenManager->parseTokenString($validTokenString));
    }

    /**
     * testInvalidParseTokenString
     */
    function testInvalidParseTokenString()
    {
        $invalidTokenString = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9'
            .'eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiYWRtaW4iOnRydWV9'
            .'TJVA95OrM7E2cBab30RMHrHDcEfxjoYZgeFONFh7HgQ';

        $this->assertEquals(null, $this->tokenManager->parseTokenString($invalidTokenString));
    }

    /**
     * testValidTokenSignature
     */
    function testValidTokenSignature()
    {
        $validToken = $this->tokenManager->createToken($this->user);
        $this->assertEquals(true, $this->tokenManager->checkTokenSignature($validToken));
    }

    /**
     * testInvalidTokenSignature
     */
    function testInvalidTokenSignature()
    {
        $invalidToken = $this->invalidTokenManager->createToken($this->user);
        $this->assertEquals(false, $this->tokenManager->checkTokenSignature($invalidToken));
    }

    /**
     * testIsTokenValid
     */
    function testIsTokenValid()
    {
        $validToken = $this->tokenManager->createToken($this->user);
        $this->assertEquals(true, $this->tokenManager->checkTokenValidity($validToken));
    }

    /**
     * testIsTokenInvalid
     */
    function testIsTokenInvalid()
    {
        $invalidTokenString = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9'
            .'.eyJpYXQiOjE0OTMwMjk3NTYsIm5iZiI6MTQ5MzAyOTc1NiwiZXhwIjoxNDkzMDI5NDU2LCJpZCI6MSwib3JpZ2luZV9jb21tZXJjaWFsZSI6MTJ9'
            .'.1VH9b4mSQdcIJCSCOzjYYPVWOmTlX0dyepbVkNiHSeE';
        $invalidToken = $this->tokenManager->parseTokenString($invalidTokenString);
        $this->assertEquals(false, $this->tokenManager->checkTokenValidity($invalidToken));
    }

}
