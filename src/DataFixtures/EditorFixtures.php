<?php

namespace App\DataFixtures;

use App\Entity\Editor;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class EditorFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        $editors = [ "Asmodee", "Cocktail Games", "Ankama Boardgames", "Days of Wonder", "Edge Entertainment", "Space Cowboys", "Ystari", "Gigamix"];

        foreach ($editors as $value) {
            $editor = new Editor();
            $editor->setName($value);
            $manager->persist($editor);
        }

        $manager->flush();
    }
}
