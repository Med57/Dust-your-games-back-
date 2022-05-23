<?php

namespace App\Controller\Api;

use App\Entity\Game;
use OpenApi\Annotations as OA;
use App\Repository\GameRepository;
use App\Controller\Api\JsonController;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * This controller is for test, we dont need for the MVP.
 * 
 * @Route("api/", name="api_games")
 * 
 * @OA\Tag(name="Games")
 * 
 */
class GameController extends JsonController
{
    /**
     * @Route("games", name="app_api_game", methods={"GET"})
     * 
     * @OA\Response(
     *     response=200,
     *     description="Returns all the games",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Game::class, groups={"group1"}))
     *      )
     * )
     */
    public function games(GameRepository $gameRepository): Response
    {
        
        $allGames = $gameRepository->findAll();

        return $this->json200(
            $allGames,
            ["group1"]
        );
    }


}
