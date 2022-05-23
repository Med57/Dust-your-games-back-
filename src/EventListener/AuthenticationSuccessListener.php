<?php

namespace App\EventListener;
use App\Entity\User;
use App\Repository\GameUserRepository;
use Symfony\Component\Security\Core\User\UserInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;

class AuthenticationSuccessListener
{

    protected $gameUserRepository;

    public function __construct(GameUserRepository $gameUserRepository)
    {
        $this->gameUserRepository = $gameUserRepository;
    }
  
    /**
    * @param AuthenticationSuccessEvent $event
    * @param GameUserRepository $gameUserRepository
    */
    public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $event)
    {
        $data = $event->getData();
        $user = $event->getUser();

        if (!$user instanceof UserInterface) {
            return;
        }

        $allGameUser = $this->gameUserRepository->findby(["user" => $user]);
        
        $countAllGames = count($allGameUser);

        $data['user'] = [
            'id'            => $user->getId(),
            'pseudo_name'   => $user->getPseudoName(),
            'email'         => $user->getEmail(),
            'year_of_birth' => $user->getYearOfBirth(),
            'image'         => $user->getImage(),
            'nb_games'      => $countAllGames
        ];

        $event->setData($data);
        
    }   
}