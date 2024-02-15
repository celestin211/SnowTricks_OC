<?php

namespace App\DataFixtures;

use App\Entity\Utilisateur;
use App\EnumTypes\EnumCiviliteType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;


use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class DataFixtureUtilisateur extends Fixture implements FixtureInterface, OrderedFixtureInterface, FixtureGroupInterface
{
    /**
     * @var UserPasswordHasherInterface
     */
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager)
    {
        $utilisateur1 = new Utilisateur();
        $utilisateur1
            ->setEmail('goldy@yahoo.fr')
            ->setCivilite(EnumCiviliteType::MONSIEUR)
            ->setNom('Goldy')
            ->setPrenom('Goldy')
            ->setRoles(['ROLE_ADMIN'])
            ->setEnabled(1);
        $utilisateur1->setPassword($this->passwordHasher->hashPassword($utilisateur1, 'babaro211'));
        $manager->persist($utilisateur1);

        $utilisateur2 = new Utilisateur();
        $utilisateur2
            ->setEmail('agathe.mbeka@yahoo.fr')
            ->setCivilite(EnumCiviliteType::MONSIEUR)
            ->setNom('BOSONGO')
            ->setPrenom('Agathe')
            ->setRoles(['ROLE_ADMIN'])
            ->setEnabled(1);
        $utilisateur2->setPassword($this->passwordHasher->hashPassword($utilisateur2, 'babaro211'));
        $manager->persist($utilisateur2);

        $utilisateur3 = new Utilisateur();
        $utilisateur3
            ->setEmail('simla.choytun@yahoo.fr')
            ->setCivilite(EnumCiviliteType::MONSIEUR)
            ->setNom('CHOYTUN')
            ->setPrenom('Simla')
            ->setRoles(['ROLE_ADMIN'])
            ->setEnabled(1);
        $utilisateur3->setPassword($this->passwordHasher->hashPassword($utilisateur3, 'babaro211'));
        $manager->persist($utilisateur3);

        $utilisateur4 = new Utilisateur();
        $utilisateur4
            ->setEmail('papy.bokoli@yahoo.fr')
            ->setCivilite(EnumCiviliteType::MONSIEUR)
            ->setNom('MBEKA')
            ->setPrenom('PAPY')
            ->setRoles(['ROLE_ADMIN'])
            ->setEnabled(1);
        $utilisateur4->setPassword($this->passwordHasher->hashPassword($utilisateur4, 'babaro211'));

        $manager->persist($utilisateur4);


        $manager->flush();
    }

    public function getOrder(): int
    {
        return 2;
    }

    /**
     * This method must return an array of groups
     * on which the implementing class belongs to.
     *
     * @return string[]
     */
    public static function getGroups(): array
    {
        return ['dev'];
    }
}
