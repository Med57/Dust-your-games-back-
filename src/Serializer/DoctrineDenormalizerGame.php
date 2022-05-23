<?php

namespace App\Serializer;

use App\Entity\Game;
use App\Entity\Editor;
use App\Entity\Category;
use App\Entity\Designor;
use App\Repository\GameRepository;
use App\Repository\EditorRepository;
use App\Repository\CategoryRepository;
use App\Repository\DesignorRepository;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/**
 * Entity denormalizer
 */
class DoctrineDenormalizerGame implements DenormalizerInterface
{

    /** @var gameRepository **/
    protected $gameRepository;
    
    /** @var designorRepository **/
    protected $designorRepository;

    /** @var editorRepository **/
    protected $editorRepository;

    /** @var categoryRepository **/
    protected $categoryRepository;



    public function __construct(GameRepository $gameRepository, EditorRepository $editorRepository, DesignorRepository $designorRepository, CategoryRepository $categoryRepository)
    {
        $this->gameRepository = $gameRepository;
        $this->editorRepository = $editorRepository;
        $this->designorRepository = $designorRepository;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * Ce denormalizer doit-il s'appliquer sur la donnée courante ?
     * Si oui, on appelle $this->denormalize()
     *
     * $data => l'id du Genre
     * $type => le type de la classe vers laquelle on souhaite dénormaliser $data
     *
     * @inheritDoc
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        // exemple pour Movie::genres
        // "genres" : [1,2]
        // $data = 1 (puis 2 car c'est un tableau)
        // $type = le FQCN de la classe à denormalizer App\Entity\Genre
        // pour savoir si je peut denormalizer, je regarde à la fois que l 'on me demande:
        // une entité
        // avec un ID (numeric)
        // TRUE si je suis capable de le faire
        //dump($data);
        //dump($type);
        return strpos($type, 'App\\Entity\\') === 0 && (key_exists("name", $data));
    }

    /**
     * Cette méthode sera appelée si la condition du dessus est valide
     * @inheritDoc
     */
    public function denormalize($data, $class, $format = null, array $context = [])
    {
        // $data = 1 (puis 2 car c'est un tableau)
        // $type = le FQCN de la classe à denormalizer App\Entity\Genre
        // avec l'entityManager on peut faire un find sans utiliser le repository
        // en donnant en plus de l'ID le FQCN de l'entité
        //dump($data);
        // ex : $this->em->find('App\Entity\Genre', 1);
        //dd($data['name']);

        $game = $this->gameRepository->findOneBy(['name' => $data['name']]);
        $editor = $this->editorRepository->findOneBy(["name" => $data["editor"]["name"]]);
        $designor = $this->designorRepository->findOneBy(["name" => $data["designor"]["name"]]);
  
        if ($game === null) {
            $game = new Game();
            $game->setName($data["name"]);
            $game->setImage($data["image"]);
            $game->setMinPlayer($data["min_player"]);
            $game->setMaxPlayer($data["max_player"]);
            $game->setPlayTime(($data["min_playtime"]+ $data["max_playtime"])/2);
            $game->setDescription($data["description"]);
            
            if ($editor === null) {
                $editor = new Editor();
                $editor->setName($data['editor']['name']);
                $this->editorRepository->add($editor, false);
            }
            $game->setEditor($editor);

            if ($designor === null) {
                $designor = new Designor();
                $designor->setName($data['designor']['name']);
                $this->designorRepository->add($designor, false);
            }
            $game->setDesignor($designor);


            foreach($data['category'] as $categoryName){
                $category = $this->categoryRepository->findBy(["name" => $categoryName['name'] ]);
    
                if ($category === []) {
                    $category = new Category();
                    $category->setName($categoryName['name']);        
                    $this->categoryRepository->add($category, false);
                    $game->addCategory($category);

                }else{
                   $game->addCategory($category[0]);
                }
                
            }
            $this->gameRepository->add($game, true);
        }
        return $game;
    }
}

