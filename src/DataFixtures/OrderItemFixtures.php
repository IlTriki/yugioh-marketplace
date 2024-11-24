<?php
namespace App\DataFixtures;

use App\Entity\OrderItem;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class OrderItemFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $orderItems = [
            [
                'order' => 'ORD-001',
                'product' => 'product_0',
                'quantity' => 2,
                'productPrice' => 50.00,
            ],
            [
                'order' => 'ORD-002',
                'product' => 'product_1',
                'quantity' => 1,
                'productPrice' => 25.00,
            ],
            [
                'order' => 'ORD-003',
                'product' => 'product_2',
                'quantity' => 3,
                'productPrice' => 15.00,
            ],
        ];

        foreach ($orderItems as $data) {
            $orderItem = new OrderItem();
            $orderItem->setOrder($this->getReference($data['order']));
            $orderItem->setProduct($this->getReference($data['product']));
            $orderItem->setQuantity($data['quantity']);
            $orderItem->setProductPrice($data['productPrice']);

            $manager->persist($orderItem);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            OrderFixtures::class,
            ProductFixtures::class,
        ];
    }
}
