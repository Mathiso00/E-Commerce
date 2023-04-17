<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class UserController extends AbstractController
{
    private $manager;

    private $userRepository;

    private $jwtManager;

    private $tokenStorageInterface;

    public function __construct(EntityManagerInterface $manager, UserRepository $userRepository, TokenStorageInterface $tokenStorageInterface, JWTTokenManagerInterface $jwtManager)
    {
        $this->manager = $manager;
        $this->userRepository = $userRepository;
        $this->jwtManager = $jwtManager;
        $this->tokenStorageInterface = $tokenStorageInterface;
    
    }


    #[Route('api/user', name: 'get_all_users', methods: ['GET'])]
    public function getUserAuth(): JsonResponse
    {
        $decodedJwtToken = $this->jwtManager->decode($this->tokenStorageInterface->getToken());
        return $this->json([
            'status' => true,
            'user' => $decodedJwtToken["email"]
        ]);
    }
}
