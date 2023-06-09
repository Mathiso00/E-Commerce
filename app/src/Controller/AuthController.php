<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\UserService;
use App\Service\ResponseService;
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
    private $responseService;
    
    public function __construct(EntityManagerInterface $manager, UserRepository $userRepository, UserService $userService, ResponseService $responseService)
    {
        $this->manager = $manager;
        $this->userRepository = $userRepository;
        $this->userService = $userService;
        $this->responseService = $responseService;
    }
    
    #[Route('api/register', name: 'user_create', methods: ['POST'])]
    public function userCreate(Request $request, UserPasswordHasherInterface $passwordEncoder): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if(isset($data['email']) && isset($data['password'])) {
            try {
                $email=$data['email'];
                $password=$data['password'];
    
                //Check if email already used
                $check_email= $this->userRepository->findOneByEmail($email);
                
                if(!$check_email) {
                    $user = new User();
    
                    $this->passwordRequirement($password);
                    $user->setPassword($password);
                    $hashedPassword = $passwordEncoder->hashPassword($user, $user->getPassword());
                    
                    $user->setEmail($email)
                    ->setPassword($hashedPassword)
                    ->setFirstname(isset($newData['firstname']) && $this->userService->Sanitize($newData['firstname']))
                    ->setLastname(isset($newData['lastname']) && $this->userService->Sanitize($newData['lastname']))
                    ->setLogin(isset($newData['login']) && $this->userService->Sanitize($newData['login']));
                    
                    $this->manager->persist($user);
                    $this->manager->flush();

                    return $this->responseService->returnStringMessage("User created !", JsonResponse::HTTP_CREATED);
                }
                return $this->responseService->returnErrorMessage("Email already used !", JsonResponse::HTTP_CONFLICT);
                
            } catch (\Exception $e) {
                return $this->responseService->returnErrorMessage($e->getMessage(), $e->getCode() ?: 500);
            }
        }
        return $this->responseService->returnErrorMessage("Missing email and/or password, I can't register you", JsonResponse::HTTP_BAD_REQUEST);
    }
    
    public function passwordRequirement($password)
    {
        $regex = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{6,}$/';
        if (!preg_match($regex, $password)) {
            throw new \Exception("The password doesn't meet the requirements", JsonResponse::HTTP_BAD_REQUEST);
        }
    }
}
