<?php

namespace App\DataFixtures;

use App\Entity\Card;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CardFixture extends Fixture
{
    public const CARD_REFERENCE = 'card_';

    public function load(ObjectManager $manager): void
    {
        $jsonFilePath = __DIR__ . '/../../assets/data/cardDB.json';
        $jsonData = json_decode(file_get_contents($jsonFilePath), true);

        foreach ($jsonData['data'] as $index => $cardData) {
            $card = new Card();
            $card->setName($cardData['name']);
            $card->setType($cardData['type']);
            $card->setFrameType($cardData['frameType']);
            $card->setDescription($cardData['desc']);
            $card->setAtk($cardData['atk']);
            $card->setDef($cardData['def']);
            $card->setLevel($cardData['level']);
            $card->setRace($cardData['race']);
            $card->setAttribute($cardData['attribute']);

            $manager->persist($card);

            $this->addReference(self::CARD_REFERENCE . $index, $card);
        }

        $manager->flush();
    }
}
