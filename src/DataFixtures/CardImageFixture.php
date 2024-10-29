<?php

namespace App\DataFixtures;

use App\Entity\CardImage;
use App\Repository\CardRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CardImageFixture extends Fixture
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

            // Create the card images for each card
            foreach ($cardData['card_images'] as $imageData) {
                $cardImage = new CardImage();
                $cardImage->setCard($cardReference);
                $cardImage->setImageUrl($imageData['image_url']);
                $cardImage->setImageUrlSmall($imageData['image_url_small']);
                $cardImage->setImageUrlCropped($imageData['image_url_cropped']);

                $manager->persist($cardImage);
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
