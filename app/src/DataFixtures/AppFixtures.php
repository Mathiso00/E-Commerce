<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{

    public function load(ObjectManager $manager)
    {
        $productNames = [
            'Laptop computer', 
            'Smartphone', 
            'Tablet', 
            'Headphones', 
            'Bluetooth speaker', 
            'Connected watch', 
            'Digital camera', 
            'External hard drive', 
            'Wireless router', 
            'Printer'
        ];

        $productDescriptions = [
            'Powerful and light laptop', 
            'High-end smartphone with superb image quality', 
            'Versatile tablet for business and personal use', 
            'Wireless headphones with outstanding audio quality', 
            'Portable bluetooth speaker for music on the go', 
            'Smart connected watch to monitor your health and fitness', 
            'High-end digital camera with exceptional resolution', 
            'Large capacity external hard drive to store your important files', 
            'Wireless router for fast and reliable Internet connectivity', 
            'Multifunction printer for home or office printing', 
            'High-end digital camera with exceptional resolution', 
            'Large capacity external hard drive to store your important files', 
            'Wireless router for fast and reliable Internet connectivity', 
            'Multifunction printer for home or office printing'
        
        ];

        $productPrices = [
            899, 1299, 399, 299, 129, 
            499, 999, 199, 79, 249
        ];

        $productPhotos = [
            'https://example.com/product1.jpg', 
            'https://example.com/product2.jpg', 
            'https://example.com/product3.jpg', 
            'https://example.com/product4.jpg', 
            'https://example.com/product5.jpg', 
            'https://example.com/product6.jpg', 
            'https://example.com/product7.jpg',
            'https://example.com/product8.jpg', 
            'https://example.com/product9.jpg', 
            'https://example.com/product10.jpg'
        ];

        // create 10 products with unique names, descriptions, prices, and photos
        for ($i = 0; $i < 10; $i++) {
            $product = new Product();
            $product->setName($productNames[array_rand($productNames)]);
            $product->setDescription($productDescriptions[array_rand($productDescriptions)]);
            $product->setPhoto($productPhotos[array_rand($productPhotos)]);
            $product->setPrice($productPrices[array_rand($productPrices)]);
            $manager->persist($product);
        }

        $manager->flush();
    }
}

