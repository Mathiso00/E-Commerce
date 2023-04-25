<?php

namespace App\Controller;

use App\Entity\Order;
use App\Service\UserService;
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

    public function __construct(UserService $userService, EntityManagerInterface $entityManagerInterface)
    {
        $this->userService = $userService;
        $this->entityManager = $entityManagerInterface;
    }

    #[Route('', name: "get_user_orders", methods: ['GET'])]
    public function getUserOrders(SerializerInterface $serializer): JsonResponse
    {
        try {
            $user = $this->userService->getUserByToken(); // Get the current user
            $orders = $user->getOrders();
    
            if (sizeof($orders) === 0) {
                return new JsonResponse(['error' => 'You have no orders.'], JsonResponse::HTTP_NOT_FOUND);
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
            return new JsonResponse([
                'status' => JsonResponse::HTTP_OK,
                'orders' => json_decode($serializedOrders),
            ]);
        } catch (\Exception $e) {
            return new JsonResponse($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    /**
     * @Route("/{orderId}", name="get_order", methods={"GET"})
     */
    public function getOrder(int $orderId, EntityManagerInterface $entityManager, SerializerInterface $serializer): JsonResponse
    {
        // Get the order from the database
        $order = $entityManager->getRepository(Order::class)->find($orderId);

        // If the order does not exist, return a 404 response
        if (!$order) {
            return new JsonResponse(['status' => 404, 'message' => 'Order not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        // Define a custom handler for circular references
        $circularReferenceHandler = function ($object) {
            return $object->getId();
        };

        // Serialize the orders into JSON format, excluding the "user" property to avoid circular reference
        $serializedOrder = $serializer->serialize($order, 'json', [
            AbstractNormalizer::IGNORED_ATTRIBUTES => ['user'],
            'circular_reference_handler' => $circularReferenceHandler,
        ]);

        // Return the order as a JSON response
        return new JsonResponse([
            'status' => JsonResponse::HTTP_OK,
            'order' => json_decode($serializedOrder),
        ]);
    }
}
