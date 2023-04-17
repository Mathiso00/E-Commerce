<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class AuthController extends AbstractController
{
    private $manager;
    
    private $userRepository;
    
    public function __construct(EntityManagerInterface $manager, UserRepository $userRepository)
    {
        $this->manager = $manager;
        $this->userRepository = $userRepository;
    }
    
    
    #[Route('api/register', name: 'user_create', methods: ['POST'])]
    public function userCreate(Request $request, UserPasswordHasherInterface $passwordEncoder): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        $email=$data['email'];
        $password=$data['password'];
        
        //Check if email already used
        $check_email= $this->userRepository->findOneByEmail($email);
        
        if($check_email) {
            return $this->json([
                'status' => false,
                'message' => 'Email already used !'
            ]);
        } else {
            $user = new User();
            
            $user->setPassword($password);
            $hashedPassword = $passwordEncoder->hashPassword($user, $user->getPassword());
            
            $user->setEmail($email)
            ->setPassword($hashedPassword)
            ->setFirstname("Firstname")
            ->setLastname("Lastname");
            
            $this->manager->persist($user);
            $this->manager->flush();
            
            return $this->json([
                'status' => true,
                'message' => 'User created !'
            ]);
        }
        
    }
}
