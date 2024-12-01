<?php
namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $admin = new User();
        $admin->setUsername('admin')
            ->setEmail('admin@example.com')
            ->setFirstName('Admin')
            ->setLastName('User')
            ->setRole('ROLE_ADMIN');

        $hashedPassword = $this->passwordHasher->hashPassword($admin, 'adminpassword');
        $admin->setPasswordHash($hashedPassword);

        $manager->persist($admin);

        for ($i = 1; $i <= 5; $i++) {
            $user = new User();
            $user->setUsername('user' . $i)
                ->setEmail("user$i@example.com")
                ->setFirstName('Name' . $i)
                ->setLastName('Surname' . $i)
                ->setRole('ROLE_USER');

            $hashedPassword = $this->passwordHasher->hashPassword($user, 'password' . $i);
            $user->setPasswordHash($hashedPassword);
            
            $manager->persist($user);
            
            $this->addReference("user$i", $user);
        }

        $manager->flush();
    }
}
