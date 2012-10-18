<?php

namespace WinkMurder\Bundle\GameBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Murder {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Game")
     */
    protected $game;

    /**
     * @ORM\OneToOne(targetEntity="Player", inversedBy="murder")
     */
    protected $victim;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $timeOfOffense;

    public function __construct(Game $game, Player $victim, \DateTime $timeOfOffense) {
        $victim->setMurder($this);
        $this->game = $game;
        $this->victim = $victim;
        $this->timeOfOffense = $timeOfOffense;
    }

    public function getId() {
        return $this->id;
    }

    public function getGame() {
        return $this->game;
    }

    public function getTimeOfOffense() {
        return $this->timeOfOffense;
    }

    public function getVictim() {
        return $this->victim;
    }

    public function isVictim(Player $player) {
        return $this->victim === $player;
    }

}