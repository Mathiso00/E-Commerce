<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

#[Route('/api/users')]
class UserController extends AbstractController
{
    private $userRepository;
    private $jwtManager;
    private $tokenStorageInterface;

    public function __construct(UserRepository $userRepository, TokenStorageInterface $tokenStorageInterface, JWTTokenManagerInterface $jwtManager)
    {
        $this->userRepository = $userRepository;
        $this->jwtManager = $jwtManager;
        $this->tokenStorageInterface = $tokenStorageInterface;
    }
    
    #[Route('', name: 'app_user_get', methods: ['GET'])]
    public function index(SerializerInterface $serializer): JsonResponse
    {
        $decodedJwtToken = $this->jwtManager->decode($this->tokenStorageInterface->getToken());
        if($decodedJwtToken && $decodedJwtToken["email"]) {
            $user = $this->userRepository->findOneBy(array('email' => $decodedJwtToken["email"]));
            $data = $serializer->serialize($user, 'json', [
                'groups' => ['api']
            ]);
            return new JsonResponse($data, 200, [], true);
        }
        
    }
    
    #[Route('', name: 'app_user_edit', methods: ['PUT'])]
    public function edit(Request $request): JsonResponse
    {
        // $userRepository->save($user, true);
        return new JsonResponse($request, 200, [], true);
    }

    // #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    // public function delete(Request $request, User $user, UserRepository $userRepository): Response
    // {
    //     if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
    //         $userRepository->remove($user, true);
    //     }

    //     return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    // }
}
