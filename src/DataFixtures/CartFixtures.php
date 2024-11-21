<?php
namespace App\DataFixtures;

use App\Entity\Cart;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class CartFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $carts = [
            [
                'user' => 'user1',
            ],
            [
                'user' => 'user2',
            ],
            [
                'user' => 'user3',
            ],
        ];

        foreach ($carts as $index => $data) {
            $cart = new Cart();
            $cart->setUser($this->getReference($data['user']));

            $manager->persist($cart);

            $this->addReference('cart_' . $index, $cart);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }
}
