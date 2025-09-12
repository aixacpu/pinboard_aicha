<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Pin;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        // Créer 3 utilisateurs
        $users = [];
        for ($i = 0; $i < 3; $i++) {
            $user = new User();
            $user->setFirstname($faker->firstName);
            $user->setLastname($faker->lastName);
            $user->setEmail($faker->unique()->email);
            $user->setPassword('password'); // mot de passe simple
            $user->setCreatedAt(new \DateTimeImmutable());
            $user->setUpdatedAt(new \DateTime());
            $manager->persist($user);
            $users[] = $user;
        }

        // Créer 12 pins
        for ($i = 0; $i < 12; $i++) {
            $pin = new Pin();
            $pin->setTitle($faker->sentence(3));
            $pin->setDescription($faker->paragraph);
            $pin->setCreatedAt(new \DateTimeImmutable());
            $pin->setUpdatedAt(new \DateTime());
            $pin->setUser($faker->randomElement($users));
            $manager->persist($pin);
        }

        $manager->flush();
    }
}

