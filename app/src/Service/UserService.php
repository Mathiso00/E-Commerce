<?php

namespace App\Service;

use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;


class UserService
{
    private $tokenStorageInterface;
    private $jwtManager;

    public function __construct(TokenStorageInterface $tokenStorageInterface, JWTTokenManagerInterface $jwtManager)
    {
        $this->tokenStorageInterface = $tokenStorageInterface;
        $this->jwtManager = $jwtManager;
    }

    public function getUserEmail()
    {
        $token = $this->tokenStorageInterface->getToken();
        $email = null;
        
        if ($token) {
            $user = $token->getUser();
            $jwt = $this->jwtManager->decode($token);
            $email = $jwt['email'] ?? $user->getUserIdentifier();
        }

        return $email;
    }
}
