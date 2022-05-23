<?php

namespace App\Service;

use App\Repository\GameUserRepository;
use Doctrine\ORM\EntityManagerInterface;

class UpdateWeightService
{

    private $gameUserRepository;
    private $em;

    public function __construct(GameUserRepository $gameUserRepository, EntityManagerInterface $em)
    {

        $this->gameUserRepository = $gameUserRepository;
        $this->em = $em;

    }

    
    public function updateByMonth(){

        $allGamesUsers = $this->gameUserRepository->findAll();
        
        foreach($allGamesUsers as $games){

            if ($games->getWeight() < 10) {
                $games->setWeight($games->getWeight()+1);
            }
        }

        $this->em->flush();

    }

}