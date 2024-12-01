<?php
namespace App\DataFixtures;

use App\Entity\Address;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class AddressFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $addresses = [
            [
                'street' => '123 Duelist Avenue',
                'postalCode' => '75001',
                'city' => 'Paris',
                'country' => 'France',
                'user' => 'user1', // Reference from UserFixtures
            ],
            [
                'street' => '456 Booster Street',
                'postalCode' => '10001',
                'city' => 'New York',
                'country' => 'USA',
                'user' => 'user2',
            ],
            [
                'street' => '789 Card Alley',
                'postalCode' => '10115',
                'city' => 'Berlin',
                'country' => 'Germany',
                'user' => 'user3',
            ],
        ];

        foreach ($addresses as $data) {
            $address = new Address();
            $address->setStreet($data['street']);
            $address->setPostalCode($data['postalCode']);
            $address->setCity($data['city']);
            $address->setCountry($data['country']);

            $user = $this->getReference($data['user']);
            $address->setUser($user);

            $manager->persist($address);
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
