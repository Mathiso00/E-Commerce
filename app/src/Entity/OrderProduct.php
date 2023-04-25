<?php

namespace App\Entity;

use App\DTO\ProductDTO;
use App\Repository\OrderProductRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Ignore;
use Symfony\Component\Serializer\Annotation\SerializedName;

#[ORM\Entity(repositoryClass: OrderProductRepository::class)]
#[ORM\Table(name: 'order_product')]
class OrderProduct
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Ignore]
    private ?int $id = null;
    
    #[ORM\ManyToOne(inversedBy: 'orderProducts')]
    #[ORM\JoinColumn(nullable: false)]
    #[Ignore]
    private ?Order $order;
    
    #[ORM\ManyToOne(inversedBy: 'orderProducts')]
    #[SerializedName(' ')]
    #[Ignore]
    private ?Product $product;
    
    #[ORM\Column(type: Types::INTEGER)]
    #[Ignore]
    private ?int $quantity = 1;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrder(): ?Order
    {
        return $this->order;
    }

    public function setOrder(?Order $order): self
    {
        $this->order = $order;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getProductDTO(): ProductDTO
    {
        return new ProductDTO(
            $this->getProduct()->getId(),
            $this->getProduct()->getName(),
            $this->getProduct()->getDescription(),
            $this->getProduct()->getPhoto(),
            $this->getProduct()->getPrice(),
            $this->getQuantity()
        );
    }
}
