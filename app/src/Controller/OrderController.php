<?php

namespace App\Controller;

use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/api/orders", name="order_")
 */
class OrderController extends AbstractController
{
    /**
     * @Route("/", name="get_user_orders", methods={"GET"})
     */
    public function getUserOrders(EntityManagerInterface $entityManager, SerializerInterface $serializer): JsonResponse
    {
        $user = $this->getUser(); // Get the current user
        $userId = $user->getId();

        // Get all orders for the current user from your database or data store
        $orders = $entityManager->getRepository(Order::class)->findBy(['user' => $userId]);
        if ($orders->count() === 0) {
            return new JsonResponse(['message' => 'You have no orders.'], JsonResponse::HTTP_BAD_REQUEST);
        }

         // Define a custom handler for circular references
        $circularReferenceHandler = function ($object) {
            return $object->getId();
        };

        // Serialize the orders into JSON format, excluding the "user" property to avoid circular reference
        $serializedOrders = $serializer->serialize($orders, 'json', [
            AbstractNormalizer::IGNORED_ATTRIBUTES => ['user'],
            'circular_reference_handler' => $circularReferenceHandler,
        ]);

        // Return the orders as a JSON response
        return new JsonResponse([
            'status' => 200,
            'orders' => json_decode($serializedOrders),
        ]);
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
            'status' => 200,
            'order' => json_decode($serializedOrder),
        ]);
    }
}
