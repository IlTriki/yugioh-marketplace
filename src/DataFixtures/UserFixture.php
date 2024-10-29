<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixture extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // Créer un utilisateur admin
        $admin = new User();
        $admin->setUsername('admin');
        $admin->setEmail('admin@caca.com');
        $admin->setRole('admin');
        $admin->setPasswordHash($this->passwordHasher->hashPassword($admin, 'admin123'));
        $manager->persist($admin);

        $customer = new User();
        $customer->setUsername('customer');
        $customer->setEmail('customer@caca.com');
        $customer->setRole('customer');
        $customer->setPasswordHash($this->passwordHasher->hashPassword($customer, 'customer123'));
        $manager->persist($customer);

        $manager->flush();
    }
}
