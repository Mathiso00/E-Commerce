<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\Order;
use App\Entity\Product;
use App\Entity\CartItem;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

// TODO: 204 if no content !
// TODO: CREATE FUNC FOR RETURN ORDER !

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

    #[Route('/{productId<\d+>}', name: "remove_from_cart", methods: ['DELETE'])]
    public function removeFromCart($productId): JsonResponse
    {
        try {
            $user = $this->getUserByToken();
            $cart = $user->getCart();
    
            if($cart === null) {
                return new JsonResponse("The cart doesn't exist !", JsonResponse::HTTP_NOT_FOUND);
            }

            $cartItem = $cart->getCartItemByProductId($productId);
    
            if (!$cartItem) {
                return new JsonResponse("Product not found !", JsonResponse::HTTP_NOT_FOUND);
            }
    
            $this->entityManager->remove($cartItem);
            $this->entityManager->flush();
            if($cart->getCartItems()->count() === 0) {
                $this->entityManager->remove($cart);
                $this->entityManager->flush();
            }
    
            return new JsonResponse('Item deleted', JsonResponse::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            return new JsonResponse($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    #[Route('', name: "cart_list", methods: ['GET'])]
    public function getCartList(): JsonResponse
    {
        try {
            $user = $this->getUserByToken();
            $cart = $user->getCart();
            
            if($cart === null) {
                return new JsonResponse("No cart and cart items", JsonResponse::HTTP_NO_CONTENT);
            }

            $cartItems = $cart->getCartItems();
            if ($cartItems->count() === 0) {
                return new JsonResponse("Array empty", JsonResponse::HTTP_NO_CONTENT);
            }
    
            $responseArray = [];
            foreach ($cartItems as $cartItem) {
                $responseArray[] = [
                    'product_id' => $cartItem->getProduct()->getId(),
                    'product_name' => $cartItem->getProduct()->getName(),
                    'quantity' => $cartItem->getQuantity(),
                    'price' => $cartItem->getProduct()->getPrice(),
                ];
            }
            return new JsonResponse($responseArray, JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    #[Route('/validate', name: "validate_cart", methods: ['POST'])]
    public function validateCart()
    {
        try {
            $user = $this->getUserByToken();
            $cart = $user->getCart();

            if($cart === null) {
                return new JsonResponse("No cart and cart items", JsonResponse::HTTP_NO_CONTENT);
            }
    
            $cartItems = $cart->getCartItems();
    
            if ($cartItems->count() === 0) {
                return new JsonResponse('Cart is empty', JsonResponse::HTTP_BAD_REQUEST);
            }
    
            // Calculate total price (At least 1$/€/£ cause of price check in productController)
            $totalPrice = $cart->calculateTotalPrice();

            // Create order entity
            $productList = [];
            foreach ($cartItems as $cartItem) {
                for ($i = 1; $i <= $cartItem->getQuantity() ; $i++) {
                    array_push($productList, $cartItem->getProduct()->getId());
                }
            }
            $order = new Order();
            $order->setUser($user);
            $order->setTotalPrice($totalPrice);
            $order->setCreationDate(new \DateTime());
            $order->setProductList($productList);

            // Persist and flush changes to database
            $this->entityManager->persist($order);
            $this->entityManager->remove($cart);
            $this->entityManager->flush();
    
            return new JsonResponse($order, JsonResponse::HTTP_CREATED);
        } catch (\Exception $e) {
            return new JsonResponse($e->getMessage(), $e->getCode() ?: 500);
        }
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
