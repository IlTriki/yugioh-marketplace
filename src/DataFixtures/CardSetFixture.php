<?php

namespace App\DataFixtures;

use App\Entity\CardSet;
use App\Repository\CardRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CardSetFixture extends Fixture
{
    private CardRepository $cardRepository;

    public function __construct(CardRepository $cardRepository)
    {
        $this->cardRepository = $cardRepository;
    }

    public function load(ObjectManager $manager): void
    {
        $jsonFilePath = __DIR__ . '/../../assets/data/cardDB.json';
        $jsonData = json_decode(file_get_contents($jsonFilePath), true);

        foreach ($jsonData['data'] as $index => $cardData) {
            $cardReference = $this->getReference(CardFixture::CARD_REFERENCE . $index);  // Get the card reference

            // Create the card sets for each card
            foreach ($cardData['card_sets'] as $setData) {
                $cardSet = new CardSet();
                $cardSet->setCard($cardReference);
                $cardSet->setSetName($setData['set_name']);
                $cardSet->setSetCode($setData['set_code']);
                $cardSet->setSetRarity($setData['set_rarity']);
                $cardSet->setSetRarityCode($setData['set_rarity_code']);
                $cardSet->setSetPrice(floatval($setData['set_price']));

                $manager->persist($cardSet);
            }
        }
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            CardFixture::class,
        ];
    }
}