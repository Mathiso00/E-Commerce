<?php

namespace App\Controller;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/api/products", name="product_")
 */
class ProductController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     */
    public function index(EntityManagerInterface $entityManager, SerializerInterface $serialize): Response
    {
        $products = $entityManager->getRepository(Product::class)->findAll();
        $data = $serialize->serialize($products, 'json');
        return new Response($data, 200, [
            'Content-Type' => 'application/json'
        ]);
    }

     /**
     * @Route("/{productId}", name="show", methods={"GET"})
     */
    public function show(EntityManagerInterface $entityManager, SerializerInterface $serializer, int $productId): JsonResponse
    {
        $product = $entityManager->getRepository(Product::class)->find($productId);

        if(!$product){
            return new JsonResponse(['status' => 404, 'message' => 'Product not found'], Response::HTTP_NOT_FOUND);
        }

        $data = $serializer->serialize($product, 'json');

        return new JsonResponse($data, JsonResponse::HTTP_OK, [], true);
    }

        /**
     * @Route("/", name="create", methods={"POST"})
     */
    public function create(Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer): JsonResponse
    {
        $product = $serializer->deserialize($request->getContent(), Product::class, 'json');
        $entityManager->persist($product);
        $entityManager->flush();

        return new JsonResponse($serializer->serialize($product, 'json'), JsonResponse::HTTP_CREATED, ['status' => 201, 'message' => 'Product created successfully'], true);
    }

    /**
     * @Route("/{productId}", name="update", methods={"PUT"})
     */
    public function update(Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer, int $productId): JsonResponse
    {
        $product = $entityManager->getRepository(Product::class)->find($productId);

        if (!$product) {
            return new JsonResponse(['status' => 404, 'message' => 'Product not found'], Response::HTTP_NOT_FOUND);
        }

        $serializer->deserialize($request->getContent(), Product::class, 'json', ['object_to_populate' => $product]);
        $entityManager->flush();

        return new JsonResponse($serializer->serialize($product, 'json'), JsonResponse::HTTP_OK, ['status' => 200, 'message' => 'Product update successfully'], true);
    }

    /**
     * @Route("/{productId}", name="delete", methods={"DELETE"})
     */
    public function delete(EntityManagerInterface $entityManager, int $productId): Response
    {
        $product = $entityManager->getRepository(Product::class)->find($productId);

        if (!$product) {
            return new JsonResponse(['status' => 404, 'message' => 'Produit not found'], Response::HTTP_NOT_FOUND);
        }

        $entityManager->remove($product);
        $entityManager->flush();

        // Retourner une réponse HTTP 204 No Content pour indiquer que la suppression a réussi
        return new Response('Product deleted successfully', Response::HTTP_NO_CONTENT);
    }

    //     /**
    // * @Route("/cart/add/{productId}", name="cart_add", methods={"POST"})
    // */
    // public function addToCart(Request $request, EntityManagerInterface $entityManager, int $productId): JsonResponse
    // {
    //     // Vérifier si le produit existe
    //     $product = $entityManager->getRepository(Product::class)->find($productId);
    //     if (!$product) {
    //         return new JsonResponse(['status' => 404, 'message' => 'Product not found'], Response::HTTP_NOT_FOUND);
    //     }

    //     // Récupérer le panier de l'utilisateur (ou le créer s'il n'existe pas encore)
    //     $user = $this->getUser();
    //     $cart = $entityManager->getRepository(Cart::class)->findOneBy(['user' => $user]);
    //     if (!$cart) {
    //         $cart = new Cart();
    //         $cart->setUser($user);
    //         $entityManager->persist($cart);
    //     }

    //     // Ajouter le produit au panier
    //     $cartItem = new CartItem();
    //     $cartItem->setProduct($product);
    //     $cartItem->setQuantity(1); // Mettez la quantité souhaitée ici
    //     $cartItem->setCart($cart);
    //     $entityManager->persist($cartItem);
    //     $entityManager->flush();

    //     return new JsonResponse(['status' => 200, 'message' => 'Product added to cart successfully']);
    // }

}
