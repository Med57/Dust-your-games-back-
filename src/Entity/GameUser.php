<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\GameUserRepository;
use DateTime;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=GameUserRepository::class)
 * @ORM\HasLifecycleCallbacks()
 */
class GameUser
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups("show_delete_game")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Game::class, inversedBy="gameUsers")
     * @Groups("show_games") 
     * @Groups("show_category")
     * @Groups("show_one_game")
     * @Groups("show_one_category")
     * @Groups("show_delete_game")
     * @Groups("show_game_weight")
     */
    private $game;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="gameUsers")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="integer")
     * @Groups("show_one_game")
     * @Groups("show_game_weight")
     */
    private $weight;

    /**
     * @ORM\Column(type="date")
     */
    private $created_at;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $updated_at;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGame(): ?Game
    {
        return $this->game;
    }

    public function setGame(?Game $game): self
    {
        $this->game = $game;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getWeight(): ?int
    {
        return $this->weight;
    }

    public function setWeight(int $weight): self
    {
        $this->weight = $weight;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(?\DateTimeInterface $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    /** 
     * @ORM\PrePersist()
     * 
    */
    public function OnPrePersist(){
        $this->created_at = new \DateTime();
        
    }

    /** 
     * @ORM\PreUpdate()
     * 
    */
    public function OnPreUpdate(){
        $this->updated_at = new \DateTime();
    }
}
