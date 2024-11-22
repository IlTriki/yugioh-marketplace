<?php
namespace App\DataFixtures;

use App\Entity\Product;
use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ProductFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $jsonFilePath = __DIR__ . '/../../assets/data/cardDB.json';
        $jsonData = json_decode(file_get_contents($jsonFilePath), true);

        $cardCategory = $this->getReference('category_card');
        $boosterCategory = $this->getReference('category_booster');
        $accessoryCategory = $this->getReference('category_accessory');

        foreach ($jsonData['data'] as $index => $cardData) {
            $product = new Product();
            $product->setName($cardData['name']);
            $product->setDescription($cardData['desc']);
            $product->setType($cardData['type'] ?? null);
            $product->setFrameType($cardData['frameType'] ?? null);
            $product->setAtk($cardData['atk'] ?? null);
            $product->setDef($cardData['def'] ?? null);
            $product->setLevel($cardData['level'] ?? null);
            $product->setRace($cardData['race'] ?? null);
            $product->setAttribute($cardData['attribute'] ?? null);
            $product->setStock(100);

            $firstSet = $cardData['card_sets'][0] ?? null;
            if ($firstSet) {
                $product->setPrice(floatval($firstSet['set_price']));
            } else {
                $product->setPrice(6.99);
            }

            $product->setCategory($cardCategory);

            $manager->persist($product);

            $this->addReference('product_' . $index, $product);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            CategoryFixtures::class,
        ];
    }
}
