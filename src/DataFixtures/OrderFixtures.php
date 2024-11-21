<?php
namespace App\DataFixtures;

use App\Entity\Order;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\Enums\OrderStatus;

class OrderFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $orders = [
            [
                'reference' => 'ORD-001',
                'createdAt' => new \DateTime('2024-01-01 10:00:00'),
                'status' => OrderStatus::IN_PREPARATION,
                'user' => 'user1',
            ],
            [
                'reference' => 'ORD-002',
                'createdAt' => new \DateTime('2024-01-02 12:00:00'),
                'status' => OrderStatus::SHIPPED,
                'user' => 'user2',
            ],
            [
                'reference' => 'ORD-003',
                'createdAt' => new \DateTime('2024-01-03 14:00:00'),
                'status' => OrderStatus::DELIVERED,
                'user' => 'user3',
            ],
        ];

        foreach ($orders as $data) {
            $order = new Order();
            $order->setReference($data['reference']);
            $order->setCreatedAt($data['createdAt']);
            $order->setStatus($data['status']);
            $order->setUser($this->getReference($data['user']));

            $manager->persist($order);

            $this->addReference($data['reference'], $order);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }
}
