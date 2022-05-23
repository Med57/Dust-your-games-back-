<?php

namespace App\DataFixtures;

use App\Entity\Game;
use App\Entity\Editor;
use App\Entity\Category;
use App\Entity\Designor;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class GameFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void 
    {
       
        $games = [ "Cluedo", "Monopoly", "La Bonne Paye", "Uno", "Scrabble", "421", "Blanc Manger Coco", "Trivial Pursuit", "Root", "Pictionnary"];

        $editorRepository   = $manager->getRepository(Editor::class);
        $designorRepository = $manager->getRepository(Designor::class);
        $categoryRepository = $manager->getRepository(Category::class);
        
        foreach ($games as $value) {

            $game = new Game;
            $game->setName($value);
            $game->setImage("https://picsum.photos/200");
            $game->setMinPlayer(rand(1, 4));
            $game->setMaxPlayer(rand(2, 8));
            $game->setPlayTime(rand(5, 120));
            $game->setDescription("Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam faucibus, leo a tristique aliquam, sem turpis tempus nibh, elementum hendrerit 
            libero justo in ligula. In ultricies tortor vitae risus venenatis, sit amet scelerisque metus porttitor. In euismod nulla a justo varius, in porttitor velit 
            fringilla. Morbi et nisi suscipit, molestie risus non, mattis enim. Maecenas tristique metus ut sollicitudin egestas. Mauris massa eros, pellentesque nec lectus id, 
            sollicitudin semper tortor. Donec ac ornare urna, sed venenatis tortor. Fusce eget facilisis lacus. Quisque laoreet euismod neque, a mattis nisi fringilla eget. Vivamus 
            bibendum commodo volutpat.");

            // Generation of one Editor from All Editor.

            $allEditor = $editorRepository->findAll();
            $randomEditor = $allEditor[rand(0, count($allEditor)-1)];
            $game->setEditor($randomEditor);

            // Generation of one Designor from All Designor.

            $allDesignor = $designorRepository->findAll();
            $randomDesignor = $allDesignor[rand(0, count($allDesignor)-1)];
            $game->setDesignor($randomDesignor);

            // Generation of category.

            $countRandom = rand(1,3);

            for($countRandom; $countRandom<4; $countRandom++){
               $allCategory = $categoryRepository->findAll();
               $randomCategory = $allCategory[rand(0, count($allCategory)-1)];

               $game->addCategory($randomCategory);
            }
            $manager->persist($game);

        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            DesignorFixtures::class,
            EditorFixtures::class,
            CategoryFixtures::class
        ];
    }
}