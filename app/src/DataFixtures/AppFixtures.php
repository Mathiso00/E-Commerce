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
            'Tablet', 
            'Connected watch',
            'Camara Sony', 
            'Mac computer',  
            'Laptop computer', 
            'Printer Canon',
            'Game PC', 
            'Gaming mouse', 
            'Keyboard PC', 
            'Bluetooth speaker'
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
            'https://media.ldlc.com/r1600/ld/products/00/05/29/47/LD0005294717_2.jpg', 
            'https://media.wired.com/photos/627c4bf186be2a18d07a0300/master/w_2580,c_limit/Google-Pixel-Watch-Gadget-Lab-Gear.jpg', 
            'https://i.jessops.com/ce-images/PRODUCT/PRODUCT_ENLARGED/ASONYCM075307454.jpg?image=600', 
            'https://images.macrumors.com/article-new/2022/06/macos-ventura-1.jpg',
            'https://images.immediate.co.uk/production/volatile/sites/3/2021/08/Best-budget-laptop-86d85a5.png', 
            'https://m.media-amazon.com/images/I/81qAy8skVEL.jpg', 
            'https://d10mhq06fikmnr.cloudfront.net/catalog/product/h/u/hunter_rev2_os_rgb-min_4_2.jpg',
            'https://www.howtogeek.com/wp-content/uploads/2017/03/shutterstock_723052858.jpg?height=200p&trim=2,2,2,2', 
            'https://cdn.shopify.com/s/files/1/0172/4173/5222/products/247031728_1200x1200.jpg?v=1681693369', 
            'https://m.media-amazon.com/images/I/81tje+OUEhL._AC_SL1500_.jpg'
        ];

        // create 10 products with unique names, descriptions, prices, and photos
        for ($i = 0; $i < 10; $i++) {
            $product = new Product();
            $product->setName($productNames[$i]);
            $product->setDescription($productDescriptions[$i]);
            $product->setPhoto($productPhotos[$i]);
            $product->setPrice($productPrices[$i]);
            $manager->persist($product);
        }

        $manager->flush();
    }
}

