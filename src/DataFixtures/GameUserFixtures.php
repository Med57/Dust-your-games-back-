<?php

namespace App\DataFixtures;

use App\Entity\Game;
use App\Entity\User;
use App\Entity\GameUser;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class GameUserFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $gameRepository = $manager->getRepository(Game::class); // We need the game Repository for use the method findAll();
        $userRepository = $manager->getRepository(User::class); // same as game.
        $allGames = $gameRepository->findAll(); 
        $allUsers = $userRepository->findAll();

        for($counter=0; $counter<30 ; $counter++) {

            $gameUser = new GameUser();

            // We use the method findAll for make an array and use the function rand()
            $randomGames = $allGames[rand(0, count($allGames)-1)];
            $gameUser->setGame($randomGames);
            
            $randomUsers = $allUsers[rand(0, count($allUsers)-1)];
            $gameUser->setUser($randomUsers);

            $gameUser->setWeight(rand(1,10));

            $manager->persist($gameUser);
    
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
            GameFixtures::class 
        ];
    }
}
