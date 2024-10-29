<?php

namespace App\DataFixtures;

use App\Entity\OrdersItem;
use App\Repository\OrdersRepository;
use App\Repository\StockRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class OrdersItemFixture extends Fixture
{
    private OrdersRepository $ordersRepository;
    private StockRepository $stockRepository;

    public function __construct(OrdersRepository $ordersRepository, StockRepository $stockRepository)
    {
        $this->ordersRepository = $ordersRepository;
        $this->stockRepository = $stockRepository;
    }

    public function load(ObjectManager $manager): void
    {
        $orders = $this->ordersRepository->findOneBy([]);
        $stockItem = $this->stockRepository->findOneBy([]);

        $ordersItem = new OrdersItem();
        $ordersItem->setOrder($orders);
        $ordersItem->setStock($stockItem);
        $ordersItem->setQuantity(2);
        $ordersItem->setPrice($stockItem->getPrice());
        $manager->persist($ordersItem);

        $manager->flush();
    }
}
