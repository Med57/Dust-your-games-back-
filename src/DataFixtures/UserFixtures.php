<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $allUsers = ["Medhy", "Mehdi", "Alice", "Manu", "Matthieu"];
        $year = ["Medhy" => 1989, "Mehdi" => 1993, "Alice" => 1992, "Manu" => 1989, "Matthieu" => 1997];

        foreach ($allUsers as $value) {
            $user = new User();
            $user->setPseudoName($value);
            $user->setEmail($value."@user.com");
            $user->setPassword($value);
            $user->setYearOfBirth($year[$value]);
            $manager->persist($user);
        }

        $manager->flush();
    }

    
}