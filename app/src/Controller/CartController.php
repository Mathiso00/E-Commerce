<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\Order;
use App\Entity\Product;
use App\Entity\CartItem;
use App\Entity\OrderProduct;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


/**
 * @Route("/api/carts", name="cart_")
 */
class CartController extends AbstractController
{

    /**
     * @Route("/{productId<\d+>}", name="cart_add", methods={"POST"})
     */
    public function addToCart(Request $request, EntityManagerInterface $entityManager, ProductRepository $productRepository, $productId): JsonResponse
    {
        $productId = intval($productId);
        $product = $entityManager->getRepository(Product::class)->find($productId);

        if (!$product) {
            return new JsonResponse(['status' => 404, 'message' => 'Product not found!'], Response::HTTP_NOT_FOUND);
        }

        $user = $this->getUser();

        if (!$user || !$this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return new JsonResponse(['status' => 401, 'message' => 'You must be authenticated to add items to the cart'], Response::HTTP_UNAUTHORIZED);
        }

        $cart = $entityManager->getRepository(Cart::class)->findOneBy(['user' => $user]);

        if (!$cart) {
            $cart = new Cart();
            $cart->setUser($user);
            $entityManager->persist($cart);
        }

        $cartItem = new CartItem();
        $cartItem->setProduct($product);
        $cartItem->setQuantity(1);
        $cartItem->setCart($cart);
        $entityManager->persist($cartItem);
        $entityManager->flush();

        return new JsonResponse(['status' => 200, 'message' => 'Product added to cart successfully']);
    }

    /**
     * @Route("/{productId<\d+>}", name="remove_from_cart", methods={"DELETE"})
     */
    public function removeFromCart(EntityManagerInterface $entityManager, $productId)
    {
        $user = $this->getUser();
        $cart = $user->getCart();

        $cartItem = $cart->getCartItemByProductId($productId);

        if (!$cartItem) {
            return new JsonResponse(['status' => 404, 'message' => 'Product not found in cart.'], JsonResponse::HTTP_NOT_FOUND);
        }

        $cart->removeCartItem($cartItem);

        $entityManager->persist($cart);
        $entityManager->flush();

        return new JsonResponse(['status' => 204], JsonResponse::HTTP_NO_CONTENT);
    }

    /**
     * @Route(" ", name="cart_list", methods={"GET"})
     */
    public function cartList()
    {
        $user = $this->getUser();
        $cart = $user->getCart();

        $cartItems = $cart->getCartItems();

        $responseArray = [];
        foreach ($cartItems as $cartItem) {
            $responseArray[] = [
                'product_id' => $cartItem->getProduct()->getId(),
                'product_name' => $cartItem->getProduct()->getName(),
                'quantity' => $cartItem->getQuantity(),
                'price' => $cartItem->getProduct()->getPrice(),
            ];
        }

        return new JsonResponse($responseArray);
    }

    /**
    * @Route("/validate", name="validate_cart", methods={"POST"})
    */
    public function validateCart(EntityManagerInterface $entityManager)
    {
        $user = $this->getUser();
        $cart = $user->getCart();

        $cartItems = $cart->getCartItems();


        if ($cartItems->count() === 0) {
            return new JsonResponse(['message' => 'Cart is empty.'], JsonResponse::HTTP_BAD_REQUEST);
        }

        // Calculate total price
        $totalPrice = $cart->calculateTotalPrice();

        // Create order entity
        $order = new Order();
        $order->setUser($user);
        $order->setTotalPrice($totalPrice);
        $order->setCreationDate(new \DateTime());

        foreach ($cartItems as $cartItem) {

            // Create order product entity for each cart item
            $orderProduct = new OrderProduct();
            $orderProduct->setCommand($order);
            $orderProduct->setName($cartItem->getProduct()->getName());
            $orderProduct->setDescription($cartItem->getProduct()->getDescription());
            $orderProduct->setPhoto($cartItem->getProduct()->getPhoto());
            $orderProduct->setPrice($cartItem->getProduct()->getPrice());
            $entityManager->persist($orderProduct);

            // Remove cart item
            // $entityManager->remove($cartItem);
        }


        // Persist and flush changes to database
        $entityManager->persist($order);
        $entityManager->flush();


        // Return order information as response
        $orderData = [
            'id' => $order->getId(),
            'totalPrice' => $order->getTotalPrice(),
            'creationDate' => $order->getCreationDate()->format(\DateTimeInterface::ISO8601),
            'products' => $cartItems,
        ];

        foreach ($order->getOrderProducts() as $orderProduct) {
            $orderData['products'][] = [
                'id' => $orderProduct->getId(),
                'name' => $orderProduct->getName(),
                'description' => $orderProduct->getDescription(),
                'photo' => $orderProduct->getPhoto(),
                'price' => $orderProduct->getPrice(),
            ];
        }
        // Remove cart
        //dd($cart->getCartItems());
        $entityManager->remove($cart);

        return new JsonResponse(["statut"=> 201, $orderData ], JsonResponse::HTTP_CREATED);
    }


}
