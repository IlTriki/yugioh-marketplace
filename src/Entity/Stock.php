<?php

namespace App\Entity;

use App\Repository\StockRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: StockRepository::class)]
class Stock
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\ManyToOne(targetEntity: CardSet::class)]
    #[ORM\JoinColumn(nullable: false)]
    private CardSet $cardSet;

    #[ORM\Column(type: 'integer', options: ["default" => 0])]
    private int $quantity = 0;

    #[ORM\Column(type: 'decimal', precision: 8, scale: 2)]
    private float $price;

    #[ORM\OneToMany(mappedBy: 'stock', targetEntity: Cart::class)]
    private Collection $cartItems;

    #[ORM\OneToMany(mappedBy: 'stock', targetEntity: OrdersItem::class)]
    private Collection $ordersItems;

    public function __construct()
    {
        $this->cartItems = new ArrayCollection();
        $this->ordersItems = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getCardSet(): CardSet
    {
        return $this->cardSet;
    }

    public function setCardSet(CardSet $cardSet)
    {
        $this->cardSet = $cardSet;
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

    public function getCartItems(): Collection
    {
        return $this->cartItems;
    }

    public function addCartItem(Cart $cartItem)
    {
        if (!$this->cartItems->contains($cartItem)) {
            $this->cartItems[] = $cartItem;
            $cartItem->setStock($this);
        }

        return $this;
    }

    public function removeCartItem(Cart $cartItem)
    {
        if ($this->cartItems->contains($cartItem)) {
            $this->cartItems->removeElement($cartItem);
            if ($cartItem->getStock() === $this) {
                $cartItem->setStock(null);
            }
        }

        return $this;
    }

    public function getOrdersItems(): Collection
    {
        return $this->ordersItems;
    }

    public function addOrdersItem(OrdersItem $ordersItem)
    {
        if (!$this->ordersItems->contains($ordersItem)) {
            $this->ordersItems[] = $ordersItem;
            $ordersItem->setStock($this);
        }

        return $this;
    }

    public function removeOrdersItem(OrdersItem $ordersItem)
    {
        if ($this->ordersItems->contains($ordersItem)) {
            $this->ordersItems->removeElement($ordersItem);
            if ($ordersItem->getStock() === $this) {
                $ordersItem->setStock(null);
            }
        }

        return $this;
    }
}