<?php

namespace App\DataFixtures;

use App\Entity\Stock;
use App\Repository\CardSetRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class StockFixture extends Fixture
{
    private CardSetRepository $cardSetRepository;

    public function __construct(CardSetRepository $cardSetRepository)
    {
        $this->cardSetRepository = $cardSetRepository;
    }

    public function load(ObjectManager $manager): void
    {
        $cardSet = $this->cardSetRepository->findOneBy(['setName' => 'Legendary Collection']);

        $stock = new Stock();
        $stock->setCardSet($cardSet);
        $stock->setQuantity(10);
        $stock->setPrice(25.00);
        $manager->persist($stock);

        $manager->flush();
    }
}
