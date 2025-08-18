<?php


namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $categoriesData = [
            ['title' => 'Grabs', 'description' => 'Figures consistant à attraper la planche en plein air.'],
            ['title' => 'Flips', 'description' => 'Figures acrobatiques avec rotation complète.'],
            ['title' => 'Spins', 'description' => 'Figures avec rotation horizontale.'],
            ['title' => 'Slides', 'description' => 'Glissades sur les rails ou boxes.'],
            ['title' => 'Rotations', 'description' => 'Figures combinant flips et spins.'],
        ];

        foreach ($categoriesData as $key => $data) {
            $category = new Category();
            $category->setTitle($data['title']);
            $category->setDescription($data['description']);

            $manager->persist($category);

            // Optionnel : pour réutiliser ces catégories dans d'autres fixtures
            $this->addReference('category_' . $key, $category);
        }

        $manager->flush();
    }
}
