<?php

namespace WinkMurder\Bundle\GameBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Player {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Game", inversedBy="players")
     */
    protected $game;

    /**
     * @ORM\ManyToOne(targetEntity="Photo", inversedBy="players")
     * @ORM\JoinColumn(name="photo_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $photo;

    /**
     * @ORM\OneToOne(targetEntity="Murder", mappedBy="victim", cascade={"PERSIST"})
     */
    protected $murder;

    public function __construct(Game $game, Photo $photo) {
        $this->game = $game;
        $this->photo = $photo;
    }

    public function getId() {
        return $this->id;
    }

    public function getGame() {
        return $this->game;
    }

    public function getName() {
        return $this->photo->getTitle();
    }

    public function getPhoto() {
        return $this->photo;
    }

    public function setPhoto($photo) {
        $this->photo = $photo;
    }

    public function isMurderer() {
        return $this->game->isMurderer($this);
    }

    public function setMurder(Murder $murder = null) {
        $this->murder = $murder;
    }

    public function getMurder() {
        return $this->murder;
    }

    public function isDead() {
        return (boolean) $this->murder;
    }

    public function canMurder(Player $victim) {
        try {
            $this->checkMurder($victim);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function murder(Player $victim) {
        $this->checkMurder($victim);
        $this->game->kill($victim);
    }

    protected function checkMurder(Player $victim) {
        if (!$this->isMurderer()) throw new \Exception("Player {$this->getName()} is not the murderer.");
        if ($victim->isDead()) throw new \Exception("Player {$victim->getName()} is already dead.");
        if ($this === $victim) throw new \Exception("Player {$this->getName()} cannot murder himself.");
    }

}