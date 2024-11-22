<?php
namespace App\DataFixtures;

use App\Entity\Image;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ImageFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $jsonFilePath = __DIR__ . '/../../assets/data/cardDB.json';
        $jsonData = json_decode(file_get_contents($jsonFilePath), true);

        foreach ($jsonData['data'] as $index => $cardData) {
            $productReference = $this->getReference('product_' . $index);

            foreach ($cardData['card_images'] as $imageData) {
                $image = new Image();
                $image->setUrl($imageData['image_url']);
                $image->setProduct($productReference);

                $manager->persist($image);
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            ProductFixtures::class,
        ];
    }
}
