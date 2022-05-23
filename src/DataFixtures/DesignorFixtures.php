<?php

namespace App\DataFixtures;

use App\Entity\Designor;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class DesignorFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $designors = [ "Sylvie Barc", "Henri Sala", "Dominique Ehrhard", "Bruno Faidutti", "Régis Bonnessée", "Roberto Fraga", "Max Gerchambeau", "Bruno Cathala", "Ludovic Maublanc"];

        foreach ($designors as $value) {
            $designor = new Designor();
            $designor->setName($value);
            $manager->persist($designor);
        }


        $manager->flush();
    }
}
