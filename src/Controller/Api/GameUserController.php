<?php

namespace App\Controller\Api;

use App\Entity\Game;
use App\Entity\User;
use App\Entity\Category;
use App\Entity\GameUser;
use OpenApi\Annotations as OA;
use App\Repository\GameRepository;
use App\Repository\UserRepository;
use App\Controller\Api\JsonController;
use App\Entity\Designor;
use App\Entity\Editor;
use App\Repository\CategoryRepository;
use App\Repository\DesignorRepository;
use App\Repository\EditorRepository;
use App\Repository\GameUserRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Encoder\JsonDecode;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/api/", name="api_gamesUser")
 * 
 * @OA\Tag(name="User's games")
 * 
 */
class GameUserController extends JsonController
{
 
    /**
     * @Route("{user}/games", name="games", methods={"GET"})
     *
     * @OA\Response(
     *     response=200,
     *     description="Returns all the games of an user",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=GameUser::class, groups={"show_one_game"}))
     *     )
     * )
     */
    public function gamesUser(User $user, GameUserRepository $gameUser): Response
    {
        $gameList = $gameUser->findby(['user' => $user]);
        return $this->json200($gameList, ["show_one_game"]);
    }


    /**
     * @Route("{user}/games/{game}", name="game", methods={"GET"}, requirements={"user":"\d+" , "game":"\d+"})
     *
     * @OA\Response(
     *     response=200,
     *     description="Returns one game of an user",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=GameUser::class, groups={"show_one_game"}))
     *     )
     * )
     *
     */
    public function game(User $user, Game $game = null, GameUserRepository $gameUser): Response
    {
        $getOneGameUser = $gameUser->findby(["user" => $user, "game" => $game]);
        if ($getOneGameUser === []) {
            return $this->json404("Ce jeu n'est pas dans votre liste.");
        }
        return $this->json200($getOneGameUser, ["show_one_game"]);
    }

    /**
     * @Route("{user}/add", name="add_game_to_user", methods={"POST"})
     *
     * @OA\Response(
     *     response=201,
     *     description="add a game to user game list",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=GameUser::class, groups={"show_add_game"}))
     *     )
     * )
     *
     */
    public function addGameToUserList(User $user, Request $request, ValidatorInterface $validator, SerializerInterface $serialize, GameRepository $gameRepository, GameUserRepository $gameUserRepository): Response
    {
        if ($user !== $this->getUser()) {
            return $this->json("Tu t'égares petit malîn !");
        }

        $jsonContent = $request->getContent();
        $jsonDecode = json_decode($jsonContent);
        
        $jsonGame = $serialize->deserialize($jsonContent, Game::class, "json", ['groups' => ['show_add_game']]);
        
        // Validation of the object.
        $errorsList = $validator->validate($jsonGame);
        if (count($errorsList) > 0) {
            return $this->json422($errorsList);
        }

        
        $game = $gameRepository->findOneBy(['name' => $jsonGame->getName()]);
        
        $gameUser = $gameUserRepository->findOneBy(['game' => $game->getId(), 'user' => $user]);

        if ($gameUser == null) {
            $gameUser = new GameUser();
            $gameUser->setGame($game);
            $gameUser->setUser($user);
            $gameUser->setWeight($jsonDecode->weight);
            $gameUserRepository->add($gameUser, true);
            return $this->json(
                $this->json('Nouveau jeu ajouté dans votre collection.'),
                Response::HTTP_CREATED,
            );
        }

        return $this->json(
            $this->json('Ce jeu est d&jà dans votre collection.'),
            Response::HTTP_OK
        );
    }

    /**
     * @Route("{user}/games/{game}/delete", name="delete_game", methods={"DELETE"})
     *
     * @OA\Response(
     *     response=202,
     *     description="delete a game of user game list",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=GameUser::class, groups={"show_delete_game"}))
     *     )
     * )
     */
    public function deleteGame(User $user, GameUserRepository $gameUserRepository, GameUser $gameUser, Game $game): Response
    {
        if ($user !== $this->getUser()) {
            return $this->json("Tu t'égares petit malîn !");
        }

        $gametodelete = $gameUserRepository->findOneBy(["user" => $user, "game" => $game]);

        $gameUserRepository->remove($gametodelete);

        return $this->json(
            $this->json('Votre jeu a bien été supprimé de votre liste.'),
            Response::HTTP_ACCEPTED
        );
    }

    /**
     * @Route("{user}/games/dust", name="dust_games", methods={"GET"})
     *
     * @OA\Response(
     *     response=200,
     *     description="delete a game of user game list",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=GameUser::class, groups={"show_one_game"}))
     *     )
     * )
     */
    public function dustgames(User $user, GameUserRepository $gameUserRepository, GameRepository $gameRepository): Response
    {

        // if( $user !== $this->getUser()){
        //     return $this->json("Tu t'égares petit malîn !");
        // }

        $gamesUser = $gameUserRepository->findby(["user" => $user]);


        $selectionList = [];

        foreach ($gamesUser as $games) {
            $gamesWeight = $games->getWeight();
            $gamesId = $games->getGame();
            $count = 0;
            while ($gamesWeight > $count) {
                $selectionList[] = $gamesId;
                $count++;
            }
        }

        $randomGame = rand(0, count($selectionList)-1);

        $gameDusted = $selectionList[$randomGame];

        $gameDustedToJson = $gameRepository->findOneBy(["id" => $gameDusted]);

        return $this->json200($gameDustedToJson, ["show_one_game"]);
    }

    /**
     * @Route("{user}/games/dustby", name="dust_games_by", methods={"POST"})
     *
     * @OA\Response(
     *     response=200,
     *     description="delete a game of user game list",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=GameUser::class, groups={"show_delete_game"}))
     *     )
     * )
     */
    public function dustgamesby(Request $request, User $user, GameUserRepository $gameUserRepository, GameRepository $gameRepository)
    {

        // if( $user !== $this->getUser()){
        //     return $this->json("Tu t'égares petit malîn !");
        // }

        $jsonContent = $request->getContent();

        $jsonGames = json_decode($jsonContent);

        foreach ($jsonGames->game as $games) 
        {
            $gameId = $games->id;
            // dump($gameId);

            $gameForWeight = $gameUserRepository->findOneBy(["game" => $gameId, "user" => $user]);
            // dump($gameForWeight); 

            $gameWeight = $gameForWeight->getWeight();
            // dump($gameWeight);

            $count = 0;
            while ($gameWeight > $count) {
                $selectionList[] = $gameId;
                $count++;
            }
            
            
        }

        $randomGame = rand(0, count($selectionList)-1);

        $gameDusted = $selectionList[$randomGame];

        $gameDustedToJson = $gameRepository->findOneBy(["id" => $gameDusted]);

        return $this->json200($gameDustedToJson, ["show_one_game"]);
    }


    /**
     * @Route("{user}/games/adjustweight", name="adjust_weight", methods={"PATCH"})
     *
     * @OA\Response(
     *     response=200,
     *     description="adjust weight when a game get choosen",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=GameUser::class, groups={"show_game_weight"}))
     *     )
     * )
     */
    public function adjustWeight(User $user, Request $request, GameUserRepository $gameUserRepository, EntityManagerInterface $em)
    {
        $jsonContent = $request->getContent();

        $gameId = json_decode($jsonContent);

        $gameChose = $gameUserRepository->findOneBy(["game" => $gameId->game->id, "user" => $user]);
        
        // dd($gameChose);

        if($gameChose->getWeight() > 6 ){
            $gameChose->setWeight(3);
        }else{
            $gameChose->setWeight($gameChose->getWeight()-1);
        }
        
        $em->flush();

        return $this->json200($gameChose, ["show_game_weight"]);
    }   
}
