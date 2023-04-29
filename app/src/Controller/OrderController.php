<?php

namespace App\Controller;

use App\Entity\Order;
use App\Service\UserService;
use App\Service\ResponseService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api/orders', name: "order_")]
class OrderController extends AbstractController
{

    private $userService;
    private $entityManager;
    private $responseService;

    public function __construct(UserService $userService, EntityManagerInterface $entityManagerInterface, ResponseService $responseService)
    {
        $this->userService = $userService;
        $this->entityManager = $entityManagerInterface;
        $this->responseService = $responseService;
    }

    #[Route('', name: "get_user_orders", methods: ['GET'])]
    public function getUserOrders(SerializerInterface $serializer): JsonResponse
    {
        try {
            $user = $this->userService->getUserByToken();
            $orders = $user->getOrders();
    
            if (sizeof($orders) === 0) {
                return $this->responseService->returnErrorMessage('You have no orders.', JsonResponse::HTTP_NOT_FOUND);
            }

            //  Define a custom handler for circular references
            $circularReferenceHandler = function ($object) {
                return $object->getId();
            };
    
            // Serialize the orders into JSON format, excluding the "user" property to avoid circular reference
            $serializedOrders = $serializer->serialize($orders, 'json', [
                AbstractNormalizer::IGNORED_ATTRIBUTES => ['user'],
                'circular_reference_handler' => $circularReferenceHandler
            ]);
    
            // Return the orders as a JSON response
            return new JsonResponse(json_decode($serializedOrders), JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return $this->responseService->returnErrorMessage($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    #[Route('/{orderId<\d+>}', name: "get_order", methods: ['GET'])]
    public function getOrder(int $orderId, SerializerInterface $serializer): JsonResponse
    {
        try {
            $user = $this->userService->getUserByToken();
            $orders = $user->getOrders();
            $orderNumber = $orderId - 1;
    
            if (sizeof($orders) === 0) {
                return $this->responseService->returnErrorMessage('You have no orders.', JsonResponse::HTTP_NOT_FOUND);
            }
            if ($orders[$orderNumber] === null) {
                return $this->responseService->returnErrorMessage("This order doesn't exist", JsonResponse::HTTP_NOT_FOUND);
            }

            //  Define a custom handler for circular references
            $circularReferenceHandler = function ($object) {
                return $object->getId();
            };
    
            // Serialize the orders into JSON format, excluding the "user" property to avoid circular reference
            $serializedOrders = $serializer->serialize($orders[$orderNumber], 'json', [
                AbstractNormalizer::IGNORED_ATTRIBUTES => ['user'],
                'circular_reference_handler' => $circularReferenceHandler
            ]);
    
            return new JsonResponse(json_decode($serializedOrders), JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return $this->responseService->returnErrorMessage($e->getMessage(), $e->getCode() ?: 500);
        }
    }
}
