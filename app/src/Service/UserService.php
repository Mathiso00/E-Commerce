<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
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

    // Generate by default 401 if no token !
    public function getUserEmail(): String
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

    public function Sanitize($var): string
    {
        if(is_string($var)) {
            return filter_var($var, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        }
        throw new \Exception("Invalid data type. Expected a string.", 400);
    }

    public function findUserByEmail(String $email, UserRepository $userRepository): ?User
    {
        $user = $userRepository->findOneBy(array('email' => $email));
        if($user === null) {
            throw new \Exception("You didn't exist. How are you come here...", 401);
        }
        return $user;
    }
}
