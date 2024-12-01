<?php
namespace App\DataFixtures;

use App\Entity\Product;
use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\Enum\ProductStatus;

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
            $product->setStatus(ProductStatus::AVAILABLE);

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

        $product = new Product();
        $product->setName('Maze of the Master');
        $product->setDescription('Maze of the Master lets you explore the Deck ideas of your favorite Duelists, with dozens of new cards in our latest annual anime-themed booster set:\n10 new Egyptian and Trap Monster-themed cards inspired by Odion’s Battle City Deck!\n7 new cards inspired by Mizar’s “Galaxy”/”Tachyon” Deck from Yu‑Gi‑Oh! ZEXAL!\n7 new cards inspired by Kaiba’s X-Y-Z monsters from Battle City, and used again by Chazz in Yu‑Gi‑Oh! GX!\n7 new “Trickstar” cards inspired by Blue Angel’s Deck from Yu‑Gi‑Oh! VRAINS!\n7 new “Performage” monsters from Yu‑Gi‑Oh! ARC-V!\nPlus many more! 60 new cards in all to add to your Decks!');
        $product->setStock(100);
        $product->setStatus(ProductStatus::AVAILABLE);
        $product->setCategory($boosterCategory);
        $product->setPrice(19.99);
        $manager->persist($product);
        $this->addReference('product_maze_of_the_master', $product);

        $product = new Product();
        $product->setName('Rage of the Abyss');
        $product->setDescription('Rage of the Abyss splashes down with 100 new cards in total, including the second wave for the World Premiere theme that debuted in The Infinite Forbidden. It also continues the 25th anniversary celebration by including 25 Quarter Century Secret Rares, including 1 special card!\nRage of the Abyss core booster set looks like this:\n10 Secret Rares\n14 Ultra Rares\n26 Super Rares\n50 Commons');
        $product->setStock(100);
        $product->setStatus(ProductStatus::AVAILABLE);
        $product->setCategory($boosterCategory);
        $product->setPrice(13.99);
        $manager->persist($product);
        $this->addReference('product_rage_of_the_abyss', $product);

        $product = new Product();
        $product->setName('The Infinite Forbidden');
        $product->setDescription('The Infinite Forbidden is the first booster set of the 25th anniversary year! It’s a 100-card booster set that celebrates the 25th anniversary of Yu-Gi-Oh! with 25 special cards, including 1 Secret Rare! The Infinite Forbidden core booster set looks like this:\n10 Secret Rares\n14 Ultra Rares\n26 Super Rares\n50 Commons');
        $product->setStock(100);
        $product->setStatus(ProductStatus::AVAILABLE);
        $product->setCategory($boosterCategory);
        $product->setPrice(12.99);
        $manager->persist($product);
        $this->addReference('product_the_infinite_forbidden', $product);

        $product = new Product();
        $product->setName('Quazar Blazer Playmat');
        $product->setDescription('The Quazar Blazer Playmat is a 100x150cm playmat featuring the Quazar Blazer theme. It is made of high-quality materials and is designed to provide a comfortable and durable playing surface.');
        $product->setStock(100);
        $product->setStatus(ProductStatus::AVAILABLE);
        $product->setCategory($accessoryCategory);
        $product->setPrice(9.99);
        $manager->persist($product);
        $this->addReference('product_quazar_blazer_playmat', $product);

        $product = new Product();
        $product->setName('The I:P Masquerena Playmat');
        $product->setDescription('The I:P Masquerena Playmat is a 100x150cm playmat featuring the The I:P Masquerena theme. It is made of high-quality materials and is designed to provide a comfortable and durable playing surface.');
        $product->setStock(100);
        $product->setStatus(ProductStatus::AVAILABLE);
        $product->setCategory($accessoryCategory);
        $product->setPrice(9.99);
        $manager->persist($product);
        $this->addReference('product_ip_masquerena_playmat', $product);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            CategoryFixtures::class,
        ];
    }
}
