<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\UserService;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api/users')]
class UserController extends AbstractController
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
    

    #[Route('', name: 'app_user_get', methods: ['GET'])]
    public function index(SerializerInterface $serializer): JsonResponse
    {
        try {
            $email = $this->userService->getUserEmail();
            $user = $this->findUserByEmail($email);
            $data = $serializer->serialize($user, 'json', [
                'groups' => ['api']
            ]);
            if(empty($data)) {
                return new JsonResponse("Array is empty but you're auth... wht's happening ?!", 500, [], false);
            }
    
            return new JsonResponse($data, 200, [], true);
        } catch (\Exception $e) {
            $code = $e->getCode() ?: 500;
            http_response_code($code);
            echo $e->getMessage();
            exit;
        }
    }
    

    #[Route('', name: 'app_user_edit', methods: ['PATCH'])]
    public function edit(Request $request): JsonResponse
    {
        try {
            $newData = json_decode($request->getContent(), true);
            $email = $this->userService->getUserEmail();
            $user = $this->findUserByEmail($email);
    
            $user->setFirstname(isset($newData['firstname']) ? $this->userService->Sanitize($newData['firstname']) : $user->getFirstname());
            $user->setLastname(isset($newData['lastname']) ? $this->userService->Sanitize($newData['lastname']) : $user->getLastname());
            $user->setLogin(isset($newData['login']) ? $this->userService->Sanitize($newData['login']) : $user->getLogin());
            $user->setEmail(isset($newData['email']) ? $this->userService->Sanitize($newData['email']) : $user->getEmail());
            
            $this->manager->flush();
            
            return new JsonResponse("User updated", 200, [], true);
        } catch (\Exception $e) {
            $code = $e->getCode() ?: 500;
            http_response_code($code);
            echo $e->getMessage();
            exit;
        }
    }

    public function findUserByEmail(String $email): ?User
    {
        $user = $this->userRepository->findOneBy(array('email' => $email));
        if($user === null) {
            throw new \Exception("You didn't exist. How are you come here...", 401);
        }
        return $user;
    }
}
