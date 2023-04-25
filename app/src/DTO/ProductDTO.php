<?php
namespace App\DTO;

class ProductDTO
{
    private int $id;
    private string $name;
    private string $description;
    private string $photo;
    private float $price;
    private int $quantity;

    public function __construct(int $id, string $name, string $description, string $photo, float $price, int $quantity)
    {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->photo = $photo;
        $this->price = $price;
        $this->quantity = $quantity;
    }

    // Add getters here
    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getPhoto(): string
    {
        return $this->photo;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }
}
