<?php

namespace App\DataFixtures;

use App\Entity\Cart;
use App\Repository\StockRepository;
use App\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CartFixture  extends Fixture implements DependentFixtureInterface
{
    private UserRepository $userRepository;
    private StockRepository $stockRepository;

    public function __construct(UserRepository $userRepository, StockRepository $stockRepository)
    {
        $this->userRepository = $userRepository;
        $this->stockRepository = $stockRepository;
    }

    public function load(ObjectManager $manager): void
    {
        $user = $this->userRepository->findOneBy(['username' => 'customer']);
        $stockItem = $this->stockRepository->findOneBy([]);

        $cart = new Cart();
        $cart->setUser($user);
        $cart->setStock($stockItem);
        $cart->setQuantity(1);
        $manager->persist($cart);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixture::class,
            StockFixture::class,
        ];
    }
}