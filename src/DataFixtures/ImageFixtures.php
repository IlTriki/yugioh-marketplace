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

        $productReference = $this->getReference('product_maze_of_the_master');
        $image = new Image();
        $image->setUrl('https://img.yugioh-card.com/eu/wp-content/uploads/2024/11/MZTM-Display-01-US.webp');
        $image->setProduct($productReference);

        $manager->persist($image);

        $productReference = $this->getReference('product_rage_of_the_abyss');
        $image = new Image();
        $image->setUrl('https://img.yugioh-card.com/eu/wp-content/uploads/2024/06/ROTA-Display-03-EN.webp');
        $image->setProduct($productReference);

        $manager->persist($image);

        $productReference = $this->getReference('product_the_infinite_forbidden');
        $image = new Image();
        $image->setUrl('https://img.yugioh-card.com/eu/wp-content/uploads/2024/03/INFO-Display-01-EN-1.webp');
        $image->setProduct($productReference);

        $manager->persist($image);

        $productReference = $this->getReference('product_quazar_blazer_playmat');
        $image = new Image();
        $image->setUrl('https://52f4e29a8321344e30ae-0f55c9129972ac85d6b1f4e703468e6b.ssl.cf2.rackcdn.com/products/pictures/1188241.jpg');
        $image->setProduct($productReference);

        $manager->persist($image);

        $productReference = $this->getReference('product_ip_masquerena_playmat');
        $image = new Image();
        $image->setUrl('https://52f4e29a8321344e30ae-0f55c9129972ac85d6b1f4e703468e6b.ssl.cf2.rackcdn.com/products/pictures/1740505.jpg');
        $image->setProduct($productReference);

        $manager->persist($image);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            ProductFixtures::class,
        ];
    }
}
