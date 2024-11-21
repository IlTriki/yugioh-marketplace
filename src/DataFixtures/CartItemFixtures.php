<?php
namespace App\DataFixtures;

use App\Entity\CartItem;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class CartItemFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        // Sample data for CartItems
        $cartItems = [
            [
                'cart' => 'cart_0',
                'product' => 'product_0',
                'quantity' => 2,
                'priceAtAddition' => 50.00,
            ],
            [
                'cart' => 'cart_0',
                'product' => 'product_1',
                'quantity' => 1,
                'priceAtAddition' => 25.00,
            ],
            [
                'cart' => 'cart_1',
                'product' => 'product_2',
                'quantity' => 3,
                'priceAtAddition' => 15.00,
            ],
        ];

        foreach ($cartItems as $data) {
            $cartItem = new CartItem();
            $cartItem->setCart($this->getReference($data['cart']));
            $cartItem->setProduct($this->getReference($data['product']));
            $cartItem->setQuantity($data['quantity']);
            $cartItem->setPriceAtAddition($data['priceAtAddition']);

            $manager->persist($cartItem);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            CartFixtures::class,
            ProductFixtures::class,
        ];
    }
}
