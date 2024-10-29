<?php

namespace App\Entity;

use App\Repository\OrdersItemRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrdersItemRepository::class)]
class OrdersItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Orders::class, inversedBy: 'ordersItems')]
    #[ORM\JoinColumn(nullable: false)]
    private Orders $orders;

    #[ORM\ManyToOne(targetEntity: Stock::class, inversedBy: 'ordersItems')]
    #[ORM\JoinColumn(nullable: false)]
    private Stock $stock;

    #[ORM\Column(type: 'integer')]
    private int $quantity;

    #[ORM\Column(type: 'decimal', precision: 8, scale: 2)]
    private float $price;

    public function getId(): int
    {
        return $this->id;
    }

    public function getOrder(): Orders
    {
        return $this->orders;
    }

    public function setOrder(?Orders $orders)
    {
        $this->orders = $orders;
        return $this;
    }

    public function getStock(): Stock
    {
        return $this->stock;
    }

    public function setStock(?Stock $stock)
    {
        $this->stock = $stock;
        return $this;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity)
    {
        $this->quantity = $quantity;
        return $this;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(float $price)
    {
        $this->price = $price;
        return $this;
    }
}