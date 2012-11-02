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
     * @ORM\JoinColumn(name="victim_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $victim;

    /**
     * @ORM\OneToMany(targetEntity="Suspicion", mappedBy="murder", cascade={"PERSIST", "REMOVE"})
     */
    protected $suspicions;

    /**
     * @ORM\OneToMany(targetEntity="Player", mappedBy="favoriteMurder")
     */
    protected $likes;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $timeOfOffense;

    public function __construct(Game $game, Player $victim, \DateTime $timeOfOffense) {
        $victim->setMurder($this);
        $this->game = $game;
        $this->victim = $victim;
        $this->suspicions = new ArrayCollection();
        $this->likes = new ArrayCollection();
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

    public function addSuspicion(Player $witness, Player $suspect) {
        if (!$this->hasSuspicion($witness)) {
            if (!$witness->isDead()) {
                $suspicion = new Suspicion($this, $suspect, $witness);
                if (!$this->arePreliminaryProceedingsDiscontinued($suspicion->getTimestamp())) {
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
        return (boolean) $this->getSuspicion($witness);
    }

    public function getSuspicion(Player $witness) {
        foreach ($this->suspicions as $suspicion) {
            if ($suspicion->getWitness() === $witness) {
                return $suspicion;
            }
        }
    }

    public function arePreliminaryProceedingsDiscontinued(\DateTime $time = null) {
        // Die Ermittlungen enden auf jeden Fall, wenn das Spiel beendet wurde
        if ($this->game->isFinished())
            return true;

        // Die Ermittlungen enden mit dem nächsten Mord (kann nur durch Admin ausgelöst werden)
        if ($this !== $this->game->getLatestMurder())
            return true;

        // Die Ermittlungen enden, wenn alle lebenden Spieler einen Verdacht geäußert haben
        if (count($this->suspicions) == count($this->game->getAlivePlayers()))
            return true;

        // Die Ermittlungen enden auch, wenn alle lebenden Spieler außer dem Mörder einen Verdacht geäußert haben
        if (count($this->suspicions) == count($this->game->getAliveOtherPlayers($this->game->getMurderer()))) {
            if (!$this->hasSuspicion($this->game->getMurderer())) {
                return true;
            }
        }

        // Sonst enden die Ermittlungen, sobald die grundsätzlich eingestellte Zeitspanne abgelaufen ist
        if (!$time)
            $time = new \DateTime('now');
        $time = clone $time;
        $time->sub($this->game->getDurationOfPreliminaryProceedings());
        return $time > $this->timeOfOffense;
    }

    public function getEndOfPreliminaryProceedings() {
        $endOfPreliminaryProceedings = clone $this->timeOfOffense;
        $endOfPreliminaryProceedings->add($this->game->getDurationOfPreliminaryProceedings());
        return $endOfPreliminaryProceedings;
    }

    public function isClearedUp() {
        if (!$this->arePreliminaryProceedingsDiscontinued())
            return false;

        if (count($this->suspicions)) {
            $numberOfCorrectSuspicions = 0;
            foreach ($this->suspicions as $suspicion) {
                if ($suspicion->isCorrect()) {
                    $numberOfCorrectSuspicions++;
                }
            }
            return ($numberOfCorrectSuspicions / count($this->suspicions)) >= ($this->game->getRequiredPositiveSuspicionRate() / 100);
        } else {
            return false;
        }
    }

    public function isUnsolved() {
        if (!$this->arePreliminaryProceedingsDiscontinued())
            return false;

        return !$this->isClearedUp();
    }

    public function getLikes() {
        return $this->likes->toArray();
    }

}