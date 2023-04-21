<?php

namespace App\Controller;

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
            $user = $this->userService->findUserByEmail($email, $this->userRepository);
            $data = $serializer->serialize($user, 'json', [
                'groups' => ['api']
            ]);
            if(empty($data)) {
                return new JsonResponse("Array is empty but you're auth... what's happening ?!", 500);
            }
    
            return new JsonResponse($data, JsonResponse::HTTP_OK, [], true);
        } catch (\Exception $e) {
            return new JsonResponse($e->getMessage(), $e->getCode() ?: 500);
        }
    }
    

    #[Route('', name: 'app_user_edit', methods: ['PATCH'])]
    public function edit(Request $request): JsonResponse
    {
        $newData = json_decode($request->getContent(), true);
        if($newData === null || sizeof($newData) <= 0) {
            return new JsonResponse("Empty request Data", JsonResponse::HTTP_BAD_REQUEST);
        }
        try {
            $email = $this->userService->getUserEmail();
            $user = $this->userService->findUserByEmail($email, $this->userRepository);
    
            $user->setFirstname(isset($newData['firstname']) ? $this->userService->Sanitize($newData['firstname']) : $user->getFirstname());
            $user->setLastname(isset($newData['lastname']) ? $this->userService->Sanitize($newData['lastname']) : $user->getLastname());
            $user->setLogin(isset($newData['login']) ? $this->userService->Sanitize($newData['login']) : $user->getLogin());
            $user->setEmail(isset($newData['email']) ? $this->userService->Sanitize($newData['email']) : $user->getEmail());
            
            $this->manager->flush();
            
            return new JsonResponse("User updated", JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse($e->getMessage(), $e->getCode() ?: 500);
        }
    }

}
