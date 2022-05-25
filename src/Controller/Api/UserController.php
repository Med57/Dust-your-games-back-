<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Service\MailerService;
use OpenApi\Annotations as OA;
use Doctrine\ORM\EntityManager;
use App\Repository\UserRepository;
use App\Controller\Api\JsonController;
use App\Repository\GameUserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * @Route("/api", name="api_user")
 * 
 * @OA\Tag(name="User")
 * 
 */
class UserController extends JsonController
{
    /**
     * @Route("/register", name="app_api_user", methods={"POST"})
     *
     */
    public function register(MailerService $mailer,Request $request, SerializerInterface $serializer, UserRepository $userRepository, ValidatorInterface $validator, EntityManagerInterface $em, UserPasswordHasherInterface $hasher): JsonResponse
    {
        $jsonContent = $request->getContent();

        $jsonRegister = $serializer->deserialize($jsonContent, User::class, "json",["groups" => ["get_user"]]);  


        $errorsList = $validator->validate($jsonRegister);   
        if (count($errorsList) > 0) {
            return $this->json422($errorsList);
        }

        $jsonRegister->setRoles(['ROLE_USER']);
        
        $jsonRegister->setImage("default-avatar_ld0jlt.png");

        $password = $jsonRegister->getPassword();

        $hashedPassword = $hasher->hashPassword($jsonRegister, $password);

        $jsonRegister->setPassword($hashedPassword);

        $newUser = $userRepository->findOneBy(["email" => $jsonRegister->getEmail()]);

        if ($newUser === null) {

            $em->persist($jsonRegister);
            $em->flush();

            $mailer->sendEmailWelcome($jsonRegister);

            return $this->json(
                "Nouvel utilisateur enregistré.",
                Response::HTTP_CREATED);
        }

        
        return $this->json("Cet utilisateur est déjà enregistré.");
    }

    /**
     * @Route("/{user}/profil", name="show_account", methods={"GET"})
     * 
     * @OA\Response(
     *     response=200,
     *     description="delete account page",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=User::class, groups={"get_user_informations"}))
     *     )
     * )   
     */
    public function showAccount(UserRepository $userRepository, User $user, GameUserRepository $gameUserRepository)
    {   
        if( $user !== $this->getUser()){
            return $this->json("Tu t'égares petit malîn !");
         }
        
        $accountData = $userRepository->findOneBy(["id" => $user]);

        $allGameUser = $gameUserRepository->findby(["user" => $user]);

        $countAllGames = ["nbGames" => count($allGameUser)];

        $allData = [$accountData, $countAllGames];

        return $this->json200($allData, ["get_user_informations"]);

    }

    
    /**
     * @Route("/{user}/test", name="show_account", methods={"GET"})
     * 
     * @OA\Response(
     *     response=200,
     *     description="test if user connected",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=User::class, groups={"get_user_informations"}))
     *     )
     * )   
     */
    public function isUserConnected(User $user)
    {
        if ($user !== $this->getUser()) {
            return $this->json("Tu t'égares petit malîn !");
        } else {
            return $this->json("Tu peux circuler :)");
        }
    }
    
    /**
     * @Route("/{user}/profil/edit", name="edit_account", methods={"PATCH"})
     * 
     * @OA\Response(
     *     response=202,
     *     description="edit account page",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=User::class, groups={"get_user_informations"}))
     *     )
     * )   
     */
    public function editAccount(UserPasswordHasherInterface $hasher, User $user,Request $request, SerializerInterface $serialize, UserRepository $userRepository, ValidatorInterface $validator, EntityManagerInterface $em)
    {   
        if( $user !== $this->getUser()){
           return $this->json("Tu t'égares petit malîn !");
        }
        
        $jsonContent = $request->getContent();

        $jsonUser = json_decode($jsonContent);

        
        if(!empty($jsonUser->email))
        {
            $user->setEmail($jsonUser->email);
        }

        if(!empty($jsonUser->pseudo_name))
        {
            $user->setPseudoName($jsonUser->pseudo_name);
        }

        if(!empty($jsonUser->image))
        {
            $user->setImage($jsonUser->image);
        }

        if(!empty($jsonUser->password))
        {
            $hashedPassword = $hasher->hashPassword($user, $jsonUser->password);
            
            $user->setPassword($hashedPassword);
        }

        $em->persist($user);
        $em->flush();

        return $this->json201("Votre profil a bien été modifié.");

    }

    /**
     * @Route("/{user}/profil/delete", name="delete_account", methods={"DELETE"})
     * 
     * @OA\Response(
     *     response=202,
     *     description="delete account page",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=User::class, groups={"get_user"}))
     *     )
     * )   
     */
    public function deleteAccount(User $user, Request $request, UserRepository $userRepository, GameUserRepository $gameUserRepository)
    {
        if ($user !== $this->getUser()) {
            return $this->json("Tu t'égares petit malîn !");
        }

        $gameList = $gameUserRepository->findby(["user" => $user]);
        foreach ($gameList as $game) {
            $gameUserRepository->remove($game);
        }

        $accountToDelete = $userRepository->findOneBy(["email" => $user->getEmail()]);
        // dd($accountToDelete);
        $userRepository->remove($accountToDelete, true);

        return $this->json(
            $this->json('Votre compte a bien été supprimé.'),
            Response::HTTP_ACCEPTED,
        );
    }

    /**
     * @Route("/passwordlost", name="password_lost", methods={"POST"})
     * 
     * @OA\Response(
     *     response=202,
     *     description="get a new password after lost",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=User::class, groups={"get_user"}))
     *  )
     * )
     */
    public function passwordLost(Request $request, UserRepository $userRepository, UserPasswordHasherInterface $hasher, MailerService $mailer): Response
    {
        $jsonContent = $request->getContent();

        $userContent = json_decode($jsonContent);

        $user = $userRepository->findOneBy(["email" => $userContent->email]);

        if($user === null){
            return $this->json(
                "User not found",
                Response::HTTP_NOT_FOUND
            );
        }

        $bytes = openssl_random_pseudo_bytes(6);
        $pass = bin2hex($bytes) . "M1";

        $hashedPassword = $hasher->hashPassword($user, $pass);

        $user->setPassword($hashedPassword);

        

        $userRepository->add($user, true);

        $mailer->sendEmailPasswordLost($user, $pass);
    

        return $this->json(
            $this->json('un mot de passe vous a bien été envoyé.'),
            Response::HTTP_ACCEPTED,
        );
        
    }


}
