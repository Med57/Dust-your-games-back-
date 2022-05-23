<?php

namespace App\Controller\Api;

use App\Entity\Game;
use App\Entity\User;
use App\Entity\Editor;
use App\Entity\Category;
use App\Entity\Designor;
use App\Entity\GameUser;
use App\Service\MailerService;
use OpenApi\Annotations as OA;
use Doctrine\ORM\EntityManager;
use App\Repository\GameRepository;
use App\Repository\UserRepository;
use App\Repository\EditorRepository;
use App\Controller\Api\JsonController;
use App\Repository\CategoryRepository;
use App\Repository\DesignorRepository;
use App\Repository\GameUserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/api/", name="api_categories")
 * 
 * @OA\Tag(name="User's categories")
 * 
 */
class CategoryController extends JsonController
{
    /**
     * @Route("{user}/categories", name="categories", methods={"GET"})
     *
     * @OA\Response(
     *     response=200,
     *     description="Returns all category from a list of gameUser",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=GameUser::class, groups={"show_category"}))
     *     )
     * )
     */
    public function categories(User $user, GameUserRepository $gameUser, GameRepository $gameRepository, CategoryRepository $category): Response
    {
        if( $user !== $this->getUser()){
            return $this->json("Tu t'égares petit malîn !");
        }
        
        $gamesUserList = $gameUser->findby(["user" => $user]);

        return $this->json200($gamesUserList, ["show_category"]);
    }

  
}
