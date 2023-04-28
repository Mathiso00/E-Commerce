<?php

namespace App\Controller;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api/products', name: "product_")]
class ProductController extends AbstractController
{
    #[Route('', name: "show_all", methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager, SerializerInterface $serializer): JsonResponse
    {
        $products = $entityManager->getRepository(Product::class)->findAll();
        $data = $serializer->serialize($products, 'json');
        if(empty($data)) return new JsonResponse("No product found !", JsonResponse::HTTP_NO_CONTENT);
        return new JsonResponse($data, JsonResponse::HTTP_OK, [], true);
    }
    
    #[Route('/{productId<\d+>}', name: "show_one", methods: ['GET'])]
    public function show(EntityManagerInterface $entityManager, SerializerInterface $serializer, int $productId): JsonResponse
    {
        $product = $entityManager->getRepository(Product::class)->find($productId);
        
        if (!$product) {
            return new JsonResponse("Product not found !", JsonResponse::HTTP_NOT_FOUND);
        }
        
        $data = $serializer->serialize($product, 'json');
        return new JsonResponse($data, JsonResponse::HTTP_OK, [], true);
    }
        
    #[Route('', name: "create", methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer): JsonResponse
    {
        try {
            $product = $serializer->deserialize($request->getContent(), Product::class, 'json');
            $requiredProperties = ['name', 'description', 'price'];
            
            // Check if all required properties are present in the Product object
            foreach ($requiredProperties as $property) {
                $getter = 'get' . ucfirst($property);
                if (!property_exists($product, $property) || !$product->{$getter}() === null) {
                    return new JsonResponse(sprintf('Missing property: %s', $property), JsonResponse::HTTP_BAD_REQUEST);
                }
            }   
            // Check price 
            $this->checkPrice($product->getPrice());
            
            $entityManager->persist($product);
            $entityManager->flush();
            
            $data = $serializer->serialize($product, 'json');
            return new JsonResponse($data, JsonResponse::HTTP_CREATED, [], true);
        } catch (\Exception $e) {
            return new JsonResponse($e->getMessage(), $e->getCode() ?: 500);
        }
    }
    
    #[Route('/{productId<\d+>}', name: "update", methods: ['PUT'])]
    public function update(Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer, int $productId): JsonResponse
    {
        $product = $entityManager->getRepository(Product::class)->find($productId);
    
        if (!$product) {
            return new JsonResponse("Product not found !", JsonResponse::HTTP_NOT_FOUND);
        }
        if(json_decode($request->getContent(), true) === null) {
            return new JsonResponse("No data send !", JsonResponse::HTTP_BAD_REQUEST);
        }
        
        $serializer->deserialize($request->getContent(), Product::class, 'json', ['object_to_populate' => $product]);
        $entityManager->flush();
    
        return new JsonResponse($serializer->serialize($product, 'json'), JsonResponse::HTTP_OK, [], true);
    }
            
    #[Route('/{productId<\d+>}', name: "delete", methods: ['DELETE'])]
    public function delete(EntityManagerInterface $entityManager, int $productId): JsonResponse
    {
        $product = $entityManager->getRepository(Product::class)->find($productId);
            
        if (!$product) {
            return new JsonResponse("Product not found !", JsonResponse::HTTP_NOT_FOUND);
        }
    
        $entityManager->remove($product);
        $entityManager->flush();
    
        return new JsonResponse('Product deleted successfully', JsonResponse::HTTP_NO_CONTENT);
    }

    public function checkPrice($price)
    {
        if ($price <= 1) {
            throw new \Exception("Price need to be more than 1, I need to make money !", JsonResponse::HTTP_BAD_REQUEST);
        }

        if (!preg_match('/^\d+(\.\d{1,2})?$/', floatval($price))) {
            throw new \Exception("The price is not correct ! Only 2 numbers after the dot", JsonResponse::HTTP_BAD_REQUEST);
        }
    }
    
}
