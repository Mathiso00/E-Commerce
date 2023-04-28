<?php

namespace App\Controller;

use App\Entity\Product;
use App\Service\UserService;
use App\Service\ResponseService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

#[Route('/api/products', name: "product_")]
class ProductController extends AbstractController
{
    private $entitymanager;
    private $userService;
    private $responseService;

    public function __construct(EntityManagerInterface $entitymanager, UserService $userService, ResponseService $responseService)
    {
        $this->entitymanager = $entitymanager;
        $this->userService = $userService;
        $this->responseService = $responseService;
    }

    #[Route('', name: "show_all", methods: ['GET'])]
    public function index(SerializerInterface $serializer): JsonResponse
    {
        $products = $this->entitymanager->getRepository(Product::class)->findAll();
        $data = $serializer->serialize($products, 'json', [AbstractNormalizer::IGNORED_ATTRIBUTES => ['cartItems']]);
        if(empty($data)) return $this->responseService->returnErrorMessage("No product found !", JsonResponse::HTTP_NO_CONTENT);
        return new JsonResponse($data, JsonResponse::HTTP_OK, [], true);
    }
    
    #[Route('/{productId<\d+>}', name: "show_one", methods: ['GET'])]
    public function show(SerializerInterface $serializer, int $productId): JsonResponse
    {
        $product = $this->entitymanager->getRepository(Product::class)->find($productId);
        
        if (!$product) {
            return $this->responseService->returnErrorMessage("Product not found !", JsonResponse::HTTP_NOT_FOUND);
        }
        
        $data = $serializer->serialize($product, 'json', [AbstractNormalizer::IGNORED_ATTRIBUTES => ['cartItems']]);
        return new JsonResponse($data, JsonResponse::HTTP_OK, [], true);
    }
        
    #[Route('', name: "create", methods: ['POST'])]
    public function create(Request $request, SerializerInterface $serializer): JsonResponse
    {
        try {
            $product = $serializer->deserialize($request->getContent(), Product::class, 'json');
            $requiredProperties = ['name', 'description', 'price'];
            
            // Check if all required properties are present in the Product object
            foreach ($requiredProperties as $property) {
                $getter = 'get' . ucfirst($property);
                if (!property_exists($product, $property)) {
                    return $this->responseService->returnErrorMessage(sprintf('Missing property: %s', $property), JsonResponse::HTTP_BAD_REQUEST);
                }
                if($product->{$getter}() === "") {
                    return $this->responseService->returnErrorMessage(sprintf('%s is empty', $property), JsonResponse::HTTP_BAD_REQUEST);
                }
            }   
            // Check price 
            $this->checkPrice($product->getPrice());
            
            $this->entitymanager->persist($product);
            $this->entitymanager->flush();
            
            $data = $serializer->serialize($product, 'json', [AbstractNormalizer::IGNORED_ATTRIBUTES => ['cartItems']]);
            return new JsonResponse($data, JsonResponse::HTTP_CREATED, [], true);
        } catch (\Exception $e) {
            return $this->responseService->returnErrorMessage($e->getMessage(), $e->getCode() ?: 500);
        }
    }
    
    #[Route('/{productId<\d+>}', name: "update", methods: ['PUT'])]
    public function update(Request $request, SerializerInterface $serializer, int $productId): JsonResponse
    {
        $product = $this->entitymanager->getRepository(Product::class)->find($productId);
    
        if (!$product) {
            return $this->responseService->returnErrorMessage("Product not found !", JsonResponse::HTTP_NOT_FOUND);
        }
        if(json_decode($request->getContent(), true) === null || json_decode($request->getContent(), true) === []) {
            return $this->responseService->returnErrorMessage("No data send !", JsonResponse::HTTP_BAD_REQUEST);
        }
        
        $this->entitymanager->flush();
        $data = $serializer->serialize($product, 'json', [AbstractNormalizer::IGNORED_ATTRIBUTES => ['cartItems']]);
    
        return new JsonResponse($data, JsonResponse::HTTP_OK, [], true);
    }
            
    #[Route('/{productId<\d+>}', name: "delete", methods: ['DELETE'])]
    public function delete(EntityManagerInterface $entityManager, int $productId): JsonResponse
    {
        $product = $entityManager->getRepository(Product::class)->find($productId);
        
        if (!$product) {
            return $this->responseService->returnErrorMessage("Product not found !", JsonResponse::HTTP_NOT_FOUND);
        }
    
        $entityManager->remove($product);
        $entityManager->flush();
    
        return $this->responseService->returnStringMessage('Product deleted successfully', JsonResponse::HTTP_NO_CONTENT);
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
