<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=CategoryRepository::class)
 */
class Category
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups("show_add_game")
     * 
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=64)
     * @Groups("show_games")  
     * @Groups("show_one_game") 
     * @Groups("show_one_category")
     * @Groups("show_add_game")
     * @Groups("show_category")  // Peut petre causer un probleme de circular reference au pire des cas creer un nouveau groupe de serialization.
     * 
     */
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity=Game::class, mappedBy="category")
     * @Groups("show_one_category") 
     */
    private $game;

    public function __construct()
    {
        $this->game = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, Game>
     */
    public function getGame(): Collection
    {
        return $this->game;
    }

    public function addGame(Game $game): self
    {
        if (!$this->game->contains($game)) {
            $this->game[] = $game;
            $game->addCategory($this);
        }

        return $this;
    }

    public function removeGame(Game $game): self
    {
        if ($this->game_id->removeElement($game)) {
            $game->removeCategory($this);
        }

        return $this;
    }
}
