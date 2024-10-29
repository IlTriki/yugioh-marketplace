<?php

namespace App\DataFixtures;

use App\Entity\Orders;
use App\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class OrdersFixture extends Fixture
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function load(ObjectManager $manager): void
    {
        $user = $this->userRepository->findOneBy(['username' => 'customer']);
        
        $orders = new Orders();
        $orders->setOrderDate(new \DateTime());
        $orders->setUser($user);
        $orders->setStatus('Pending');
        $manager->persist($orders);

        $manager->flush();
    }
}
