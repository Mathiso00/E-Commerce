<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\CartItem;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("/api/carts", name="cart_")
 */
class CartController extends AbstractController
{
    /**
    * @Route("/{productId}", name="cart_add", methods={"POST"})
    */
        public function addToCart(Request $request, EntityManagerInterface $entityManager, int $productId): JsonResponse
    {
        $product = $entityManager->getRepository(Product::class)->find($productId);

        if(!$product){
            return new JsonResponse(['status' => 404, 'message' => 'Product not found!'], Response::HTTP_NOT_FOUND);
        }

        $user = $this->getUser();
        dd($user);
        
        if(!$user || !$this->isGranted('IS_AUTHENTICATED_FULLY')){
            return new JsonResponse(['status' => 401, 'message' => 'You must be authenticated to add items to the cart'], Response::HTTP_UNAUTHORIZED);
        }
        
        $cart = $entityManager->getRepository(Cart::class)->findOneBy(['user' => $user]);

        if(!$cart){
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
}
