<?php

namespace App\Controller;

use App\Service\UserService;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

#[Route('/api/users')]
class UserController extends AbstractController
{
    private $userRepository;
    private $userService;

    public function __construct(UserRepository $userRepository, UserService $userService)
    {
        $this->userRepository = $userRepository;
        $this->userService = $userService;
    }
    
    #[Route('', name: 'app_user_get', methods: ['GET'])]
    public function index(SerializerInterface $serializer): JsonResponse
    {
        $email = $this->userService->getUserEmail();
        if($email) {
            $user = $this->userRepository->findOneBy(array('email' => $email));
            $data = $serializer->serialize($user, 'json', [
                'groups' => ['api']
            ]);
            return new JsonResponse($data, 200, [], true);
        }
    }
    
    #[Route('', name: 'app_user_edit', methods: ['PATCH'])]
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
