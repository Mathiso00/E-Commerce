<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\UserService;
use App\Service\ResponseService;
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
    private $userService;
    private $responseService;

    public function __construct(EntityManagerInterface $manager, UserService $userService, ResponseService $responseService)
    {
        $this->manager = $manager;
        $this->userService = $userService;
        $this->responseService = $responseService;
    }
    

    #[Route('', name: 'app_user_get', methods: ['GET'])]
    public function index(SerializerInterface $serializer): JsonResponse
    {
        try {
            $user = $this->userService->getUserByToken();
            $data = $serializer->serialize($user, 'json', [
                'groups' => ['api']
            ]);
            if(empty($data)) {
                return $this->responseService->returnErrorMessage("Array is empty but you're auth... what's happening ?!", JsonResponse::HTTP_NOT_FOUND);
            }
            
            return new JsonResponse($data, JsonResponse::HTTP_OK, [], true);
        } catch (\Exception $e) {
            return $this->responseService->returnErrorMessage($e->getMessage(), $e->getCode() ?: 500);
        }
    }
    
    
    #[Route('', name: 'app_user_edit', methods: ['PATCH'])]
    public function edit(Request $request): JsonResponse
    {
        $newData = json_decode($request->getContent(), true);
        if($newData === null || sizeof($newData) <= 0) {
            return $this->responseService->returnErrorMessage("Empty request Data", JsonResponse::HTTP_BAD_REQUEST);
        }
        try {
            $user = $this->userService->getUserByToken();

            if(isset($newData['email'])) {
                $mailAlreadyTake = $this->manager->getRepository(User::class)->findOneBy(array('email' => $newData['email']));
                if($mailAlreadyTake !== null && $user->getEmail() !== $newData['email']) {
                    throw new \Exception("Email already used", JsonResponse::HTTP_CONFLICT);
                }
            }
            
            $user->setFirstname(isset($newData['firstname']) ? $this->userService->Sanitize($newData['firstname']) : $user->getFirstname());
            $user->setLastname(isset($newData['lastname']) ? $this->userService->Sanitize($newData['lastname']) : $user->getLastname());
            $user->setLogin(isset($newData['login']) ? $this->userService->Sanitize($newData['login']) : $user->getLogin());
            $user->setEmail(isset($newData['email']) ? $this->userService->Sanitize($newData['email']) : $user->getEmail());
            
            $this->manager->flush();

            return $this->responseService->returnStringMessage("User updated", JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return $this->responseService->returnErrorMessage($e->getMessage(), $e->getCode() ?: 500);
        }
    }

}
