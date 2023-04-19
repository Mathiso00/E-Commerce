<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\UserService;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AuthController extends AbstractController
{
    private $manager;
    private $userRepository;
    private $userService;
    
    public function __construct(EntityManagerInterface $manager, UserRepository $userRepository, UserService $userService)
    {
        $this->manager = $manager;
        $this->userRepository = $userRepository;
        $this->userService = $userService;
    }
    
    // Add password Regex ?
    #[Route('api/register', name: 'user_create', methods: ['POST'])]
    public function userCreate(Request $request, UserPasswordHasherInterface $passwordEncoder): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if(isset($data['email']) && isset($data['password'])) {
            $email=$data['email'];
            $password=$data['password'];

            //Check if email already used
            $check_email= $this->userRepository->findOneByEmail($email);
            
            if(!$check_email) {
                $user = new User();

                $user->setPassword($password);
                $hashedPassword = $passwordEncoder->hashPassword($user, $user->getPassword());
                
                $user->setEmail($email)
                ->setPassword($hashedPassword)
                ->setFirstname(isset($newData['firstname']) && $this->userService->Sanitize($newData['firstname']))
                ->setLastname(isset($newData['lastname']) && $this->userService->Sanitize($newData['lastname']))
                ->setLogin(isset($newData['login']) && $this->userService->Sanitize($newData['login']));
                
                $this->manager->persist($user);
                $this->manager->flush();
                return new JsonResponse("User created !", 201, [], true);

            }
            return new JsonResponse("Email already used !", 409, [], false);
        }
        
        
        
    }
}
