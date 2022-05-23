<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Game;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    
    public function load(ObjectManager $manager): void
    {
        $categories = [ "Jeux de société", "Jeux de cartes", "Jeux de dés", "Jeux de figurines", "Jeux de papier et de crayon", "Jeux de rôle", "Jeux d'adresse", "Jeux de stratégie"];
        $gameRepository = $manager->getRepository(Game::class);

        foreach ($categories as $value) {
            $category = new Category();
            $category->setName($value);
            $manager->persist($category);
        }   

        $manager->flush();
    }
}
