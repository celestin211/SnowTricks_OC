<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        // Utilisateur admin
        $admin = new User();
        $admin->setUsername('celestin');
        $admin->setEmail('celestin-snowtricks@yahoo.com');
        $admin->setPassword(
            $this->passwordEncoder->encodePassword($admin, 'snowtricks_211')
        );
        $admin->setRole('ROLE_ADMIN');
        $admin->setCreatedAt(new \DateTime());
        $admin->setConfirmed(true);
        $manager->persist($admin);

        // Plusieurs utilisateurs classiques
        for ($i = 1; $i <= 10; $i++) {
            $user = new User();
            $user->setUsername($faker->userName);
            $user->setEmail($faker->unique()->safeEmail);
            $user->setPassword(
                $this->passwordEncoder->encodePassword($user, 'snowtricks_211*')
            );
            $user->setRole('ROLE_USER');
            $user->setCreatedAt($faker->dateTimeBetween('-1 years', 'now'));
            $user->setConfirmed(true);
            $user->setAvatar("avatar");
            $user->setWebsite($faker->optional()->url);
            $user->setDescription($faker->optional()->text(200));

            $manager->persist($user);
        }

        $manager->flush();
    }
}
