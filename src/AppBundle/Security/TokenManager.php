<?php

namespace AppBundle\Security;

use AppBundle\Entity\User;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\ValidationData;
use Psr\Log\LoggerInterface;

/**
 * Class TokenManager
 * @package AppBundle\Security
 */
class TokenManager
{
    /**
     * @var LoggerInterface
     */
    private $validatationLogger;

    /**
     * @var string
     */
    private $tokenSignatureKey;

    /**
     * TokenManager constructor.
     *
     * @param LoggerInterface $validatationLogger
     * @param string          $tokenSignatureKey
     */
    function __construct(LoggerInterface $validatationLogger, $tokenSignatureKey)
    {
        $this->validatationLogger = $validatationLogger;
        $this->tokenSignatureKey  = $tokenSignatureKey;
    }

    /**
     * Create user token
     *
     * @param User $user
     * @return Token
     */
    public function createToken(User $user)
    {
        return (new Builder())
            ->setIssuedAt(time())
            ->setNotBefore(time())
            ->setExpiration(time())
            ->set('id', $user->getId())
            ->sign(new Sha256(), $this->tokenSignatureKey)
            ->getToken();
    }

    /**
     * Transforms token string in token object
     *
     * @param $tokenString
     * @return Token
     */
    public function parseTokenString($tokenString)
    {
        try {
            return (new Parser())->parse($tokenString);
        } catch (\Exception $e) {
            $this->validatationLogger->error($e->getMessage(), ['token' => (string)$tokenString]);
            return null;
        }
    }

    /**
     * Checks that the token was originally created from this API
     *
     * @param Token $token
     * @return bool
     */
    public function checkTokenSignature(Token $token)
    {
        if (!$token->verify(new Sha256(), $this->tokenSignatureKey)) {
            $this->validatationLogger->error('Token signatures do not match', ['token' => (string)$token]);

            return false;
        }

        return true;
    }

    /**
     * Checks that the token is still a valid one
     *
     * @param Token $token
     * @return bool
     */
    public function checkTokenValidity(Token $token)
    {
        $data = new ValidationData();
        $data->setCurrentTime(time());

        if (!$token->validate($data)) {
            $this->validatationLogger->error('Token is not valid.', ['token' => (string)$token]);

            return false;
        }

        return true;
    }
}
