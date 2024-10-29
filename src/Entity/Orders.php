<?php

namespace App\Entity;

use App\Repository\OrdersRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: OrdersRepository::class)]
class Orders
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'orders')]
    #[ORM\JoinColumn(nullable: false)]
    private User $user;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $orderDate;

    #[ORM\Column(type: 'string', length: 20, options: ["default" => "Pending"])]
    private string $status = 'Pending';

    #[ORM\OneToMany(mappedBy: '`orders`', targetEntity: OrdersItem::class, cascade: ['persist', 'remove'])]
    private Collection $ordersItems;

    public function __construct()
    {
        $this->ordersItems = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(?User $user)
    {
        $this->user = $user;
        return $this;
    }

    public function getOrderDate(): \DateTimeInterface
    {
        return $this->orderDate;
    }

    public function setOrderDate(\DateTimeInterface $orderDate)
    {
        $this->orderDate = $orderDate;
        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status)
    {
        $this->status = $status;
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
            $ordersItem->setOrder($this);
        }

        return $this;
    }

    public function removeOrdersItem(OrdersItem $ordersItem)
    {
        if ($this->ordersItems->contains($ordersItem)) {
            $this->ordersItems->removeElement($ordersItem);
            if ($ordersItem->getOrder() === $this) {
                $ordersItem->setOrder(null);
            }
        }

        return $this;
    }
}