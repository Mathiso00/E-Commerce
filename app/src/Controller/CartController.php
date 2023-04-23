<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\Order;
use App\Entity\Product;
use App\Entity\CartItem;
use App\Service\UserService;
use App\Entity\OrderProduct;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


/**
 * @Route("/api/carts", name="cart_")
 */
#[Route('/api/carts', name: "cart_")]
class CartController extends AbstractController
{

    private $userService;
    private $entityManager;

    public function __construct(UserService $userService, EntityManagerInterface $entityManagerInterface)
    {
        $this->userService = $userService;
        $this->entityManager = $entityManagerInterface;
    }

    #[Route('/{productId<\d+>}', name: "cart_add", methods: ['POST'])]
    public function addToCart($productId): JsonResponse
    {
        try {
            $user = $this->getUserByToken();

            $product = $this->entityManager->getRepository(Product::class)->find($productId);
            if (!$product) {
                return new JsonResponse("Product not found !", JsonResponse::HTTP_NOT_FOUND);
            }
            
            $cart = $this->entityManager->getRepository(Cart::class)->findOneBy(['user' => $user]);
            if (!$cart) {
                $cart = new Cart();
                $cart->setUser($user);
                $this->entityManager->persist($cart);
            }

            $check = $this->checkDuplication($user->getId(), $productId);
            if($check) {
                return new JsonResponse("Quantity updated", JsonResponse::HTTP_OK);
            } 

            $cartItem = new CartItem();
            $cartItem->setProduct($product);
            $cartItem->setQuantity(1);
            $cartItem->setCart($cart);
            $this->entityManager->persist($cartItem);
            $this->entityManager->flush();
    
            return new JsonResponse("Product added to cart", JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse($e->getMessage(), $e->getCode() ?: 500);
        }
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

        return new JsonResponse(["statut"=> JsonResponse::HTTP_CREATED, $orderData ], JsonResponse::HTTP_CREATED);
    }

    public function checkDuplication(int $userId, int $productId): bool
    {
        $cartItems = $this->entityManager->getRepository(CartItem::class)->findAll();
        if(sizeof($cartItems) > 0) {
            foreach ($cartItems as $value) {
                $cartProductId = $value->getProduct()->getId();
                $cartUserId = $value->getCart()->getUser()->getId();
                if($cartProductId === $productId && $cartUserId === $userId) {
                    $this->upgradeQuantity($value, $this->entityManager);
                    return true;
                }
            }
        }
        return false;
    }

    public function upgradeQuantity(CartItem $cartItem): void
    {
        $cartItem->setQuantity($cartItem->getQuantity() + 1);
        $this->entityManager->flush();
    }

    public function getUserByToken()
    {
        $userMail = $this->userService->getUserEmail();
        return $this->userService->findUserByEmail($userMail);
    }


}
