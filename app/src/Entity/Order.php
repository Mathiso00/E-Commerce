<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`order`')]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::FLOAT, precision: 7, scale: 2)]
    private ?float $totalPrice = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $creationDate = null;

    #[ORM\ManyToOne(inversedBy: 'orders')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;
    
    #[ORM\OneToMany(mappedBy: 'order', targetEntity: OrderProduct::class, cascade:['persist'])]
    private Collection $orderProducts;

    public function __construct()
    {
        $this->orderProducts = new ArrayCollection();
    }
    
    public function getId(): ?int
    {
        return $this->id;
    }
    
    public function getTotalPrice(): ?float
    {
        return $this->totalPrice;
    }

    public function setTotalPrice(float $totalPrice): self
    {
        $this->totalPrice = $totalPrice;

        return $this;
    }

    public function getCreationDate(): ?\DateTimeInterface
    {
        return $this->creationDate;
    }

    public function setCreationDate(\DateTimeInterface $creationDate): self
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, OrderProduct>
     */
    public function getProducts(): Collection
    {
        return $this->orderProducts;
    }

    public function addProductList(OrderProduct $productList): self
    {
        if (!$this->orderProducts->contains($productList)) {
            $this->orderProducts->add($productList);
        }

        return $this;
    }

    public function removeProductList(OrderProduct $productList): self
    {
        $this->orderProducts->removeElement($productList);

        return $this;
    }
}
