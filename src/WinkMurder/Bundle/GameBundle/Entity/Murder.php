<?php

namespace WinkMurder\Bundle\GameBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

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
     * @ORM\OneToMany(targetEntity="Suspicion", mappedBy="murder", cascade={"PERSIST", "DELETE"})
     */
    protected $suspicions;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $timeOfOffense;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $endOfPreliminaryProceedings;

    public function __construct(Game $game, Player $victim, \DateTime $timeOfOffense) {
        $victim->setMurder($this);
        $this->game = $game;
        $this->victim = $victim;
        $this->suspicions = new ArrayCollection();
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

    public function getSuspicions() {
        return $this->suspicions->toArray();
    }

    public function addSuspicion(Player $suspect, Player $witness) {
        if (!$this->hasSuspicion($witness)) {
            if ($witness->isDead()) {
                $suspicion = new Suspicion($this, $suspect, $witness);
                if (!$this->arePreliminaryProceedingsDiscontinued($suspect->getTimestamp())) {
                    $this->suspicions->add($suspicion);
                } else {
                    throw new \Exception("The preliminary proceedings were discontinued.");
                }
            } else {
                throw new \Exception("Player {$witness->getName()} is already dead and cannot witness.");
            }
        } else {
            throw new \Exception("Player {$witness->getName()} already has a suspicion on the murder of {$this->victim->getName()}.");
        }
    }

    public function hasSuspicion(Player $witness) {
        foreach ($this->suspicions as $suspicion) {
            if ($suspicion->getWitness() === $witness) {
                return true;
            }
        }
        return false;
    }

    public function arePreliminaryProceedingsDiscontinued(\DateTime $time = null) {
        if (!$time)
            $time = new \DateTime('now');
        $time = clone $time;
        $time->sub($this->game->getDurationOfPreliminaryProceedings());
        return $time > $this->timeOfOffense;
    }

    public function isClearedUp() {
        $numberOfCorrectSuspicions = 0;
        foreach ($this->suspicions as $suspicion) {
            if ($suspicion->isCorrect()) {
                $numberOfCorrectSuspicions++;
            }
        }
        return ($numberOfCorrectSuspicions / count($this->suspicions)) > $this->game->getRequiredCorrectSuspicionRate();
    }

}