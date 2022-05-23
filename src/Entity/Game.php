<?php

namespace App\Entity;

use App\Repository\GameRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=GameRepository::class)
 */
class Game
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups("show_add_game")
     * @Groups("show_one_game")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=128)
     * @Groups("group1")
     * @Groups("show_games") 
     * @Groups("show_one_game") 
     * @Groups("show_one_category")
     * @Groups("show_add_game")
     * @Groups("show_game_weight")
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=300)
     * @Groups("group1")
     * @Groups("show_one_game") 
     * @Groups("show_add_game")
     */
    private $image;

    /**
     * @ORM\Column(type="integer")
     * @Groups("group1")
     * @Groups("show_one_game") 
     * @Groups("show_add_game")
     */
    private $min_player;

    /**
     * @ORM\Column(type="integer")
     * @Groups("group1")
     * @Groups("show_one_game") 
     * @Groups("show_add_game")
     */
    private $max_player;

    /**
     * @ORM\Column(type="integer")
     * @Groups("group1")
     * @Groups("show_one_game") 
     * @Groups("show_add_game")
     */
    private $play_time;

    /**
     * @ORM\Column(type="text")
     * @Groups("group1")
     * @Groups("show_one_game") 
     * @Groups("show_add_game")
     */
    private $description;

    /**
     * @ORM\OneToMany(targetEntity=GameUser::class, mappedBy="game")
     */
    private $gameUsers;

    /**
     * @ORM\ManyToMany(targetEntity=Category::class, inversedBy="game")
     * @Groups("show_games")
     * @Groups("show_one_game")
     * @Groups("show_add_game")
     * @Groups("show_category")  // Peut petre causer un probleme de circular reference au pire des cas creer un nouveau groupe de serialization.
     * 
     */
    private $category;

    /**
     * @ORM\ManyToOne(targetEntity=Editor::class, inversedBy="games", cascade={"persist"})
     * @Groups("group1")
     * @Groups("show_one_game") 
     * @Groups("show_add_game") 
     */
    private $editor;

    /**
     * @ORM\ManyToOne(targetEntity=Designor::class, inversedBy="games", cascade={"persist"})
     * @Groups("group1")
     * @Groups("show_one_game") 
     * @Groups("show_add_game")
     * 
     */
    private $designor;

    public function __construct()
    {
        $this->gameUsers = new ArrayCollection();
        $this->category = new ArrayCollection();
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

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getMinPlayer(): ?int
    {
        return $this->min_player;
    }

    public function setMinPlayer(int $min_player): self
    {
        $this->min_player = $min_player;

        return $this;
    }

    public function getMaxPlayer(): ?int
    {
        return $this->max_player;
    }

    public function setMaxPlayer(int $max_player): self
    {
        $this->max_player = $max_player;

        return $this;
    }

    public function getPlayTime(): ?int
    {
        return $this->play_time;
    }

    public function setPlayTime(int $play_time): self
    {
        $this->play_time = $play_time;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection<int, GameUser>
     */
    public function getGameUsers(): Collection
    {
        return $this->gameUsers;
    }

    public function addGameUser(GameUser $gameUser): self
    {
        if (!$this->gameUsers->contains($gameUser)) {
            $this->gameUsers[] = $gameUser;
            $gameUser->setGame($this);
        }

        return $this;
    }

    public function removeGameUser(GameUser $gameUser): self
    {
        if ($this->gameUsers->removeElement($gameUser)) {
            // set the owning side to null (unless already changed)
            if ($gameUser->getGame() === $this) {
                $gameUser->setGame(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Category>
     */
    public function getCategory(): Collection
    {
        return $this->category;
    }

    public function addCategory(Category $category): self
    {
        if (!$this->category->contains($category)) {
            $this->category[] = $category;
        }

        return $this;
    }

    public function removeCategory(Category $category): self
    {
        $this->category->removeElement($category);

        return $this;
    }

    public function getEditor(): ?Editor
    {
        return $this->editor;
    }

    public function setEditor(?Editor $editor): self
    {
        $this->editor = $editor;

        return $this;
    }

    public function getDesignor(): ?Designor
    {
        return $this->designor;
    }

    public function setDesignor(?Designor $designor): self
    {
        $this->designor = $designor;

        return $this;
    }
}
