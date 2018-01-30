<?php

namespace AppBundle\Security;

/**
 * Class PasswordEncoder
 * @package AppBundle\Security
 */
class PasswordEncoder
{
    public function encrypt(string $password):string
    {
        return hash('sha256', $password);
    }
}