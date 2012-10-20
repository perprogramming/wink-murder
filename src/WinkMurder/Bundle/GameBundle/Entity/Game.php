<?php

namespace WinkMurder\Bundle\GameBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="WinkMurder\Bundle\GameBundle\Entity\GameRepository")
 */
class Game {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="PhotoSet")
     */
    protected $photoSet;

    /**
     * @ORM\OneToMany(targetEntity="Player", mappedBy="game", cascade={"PERSIST", "REMOVE"}, orphanRemoval=true)
     */
    protected $players;

    /**
     * @ORM\ManyToOne(targetEntity="Player")
     */
    protected $murderer;

    /**
     * @ORM\OneToMany(targetEntity="Murder", mappedBy="game", cascade={"PERSIST", "REMOVE"}, orphanRemoval=true)
     * @ORM\OrderBy({"timeOfOffense" = "ASC"})
     */
    protected $murders;

    /**
     * @ORM\Column(type="float")
     */
    protected $requiredPositiveSuspicionRate;
    /**
     * @ORM\Column(type="integer")
     */
    protected $durationOfPreliminaryProceedingsInMinutes;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $finished = false;

    public function __construct(PhotoSet $photoSet, $durationOfPreliminaryProceedingsInMinutes, $requiredPositiveSuspicionRate) {
        $this->photoSet = $photoSet;
        $this->durationOfPreliminaryProceedingsInMinutes = $durationOfPreliminaryProceedingsInMinutes;
        $this->requiredPositiveSuspicionRate = $requiredPositiveSuspicionRate;
        $this->players = new ArrayCollection();
        $this->murders = new ArrayCollection();
    }

    public function getId() {
        return $this->id;
    }

    public function getPhotoSet() {
        return $this->photoSet;
    }

    public function getUnusedPhotos() {
        $unusedPhotos = array();
        foreach ($this->photoSet->getPhotos() as $photo) {
            $found = false;
            foreach ($this->players as $player) {
                if ($player->getPhoto() === $photo) {
                    $found = true;
                    break;
                }
            }
            if (!$found)
                $unusedPhotos[] = $photo;
        }
        return $unusedPhotos;
    }

    public function findPlayer($id) {
        foreach ($this->players as $player) {
            if ($player->getId() == $id) {
                return $player;
            }
        }
    }

    public function getPlayers() {
        return $this->players->toArray();
    }

    public function addPlayer(Photo $photo, MannerOfDeath $mannerOfDeath) {
        $player = new Player($this, $photo, $mannerOfDeath);
        $this->players->add($player);
        return $player;
    }

    public function removePlayer(Player $player) {
        $this->players->removeElement($player);
    }

    public function setMurderer(Player $player = null) {
        if ($player && ($player->getGame() !== $this))
            throw new \Exception("Only players of a specific game can become murderer within it.");

        $this->murderer = $player;
    }

    public function getMurderer() {
        return $this->murderer;
    }

    public function isMurderer(Player $player) {
        return $this->murderer === $player;
    }

    public function checkKill(Player $victim, Player $murderer = null) {
        if ($murderer && !$murderer->isMurderer()) throw new \Exception("Player {$this->getName()} is not the murderer.");
        if ($victim->isDead()) throw new \Exception("Player {$victim->getName()} is already dead.");
        if ($murderer && ($murderer === $victim)) throw new \Exception("Player {$this->getName()} cannot murder himself.");
    }

    public function kill(Player $player, Player $murderer = null) {
        $this->checkKill($player, $murderer);
        $this->murders->add(new Murder($this, $player, new \DateTime('now')));
    }

    public function resurrect(Player $player) {
        if ($murder = $player->getMurder()) {
            $this->murders->removeElement($murder);
            $player->setMurder(null);
        } else {
            throw new \Exception("Player {$player->getName()} is alive.");
        }
    }

    /** @return Murder */
    public function getLatestMurder() {
        return $this->murders->last();
    }

    public function hasSuspicion(Player $witness) {
        return $this->getLatestMurder()->hasSuspicion($witness);
    }

    public function addSuspicion(Player $suspect, Player $witness) {
        $this->getLatestMurder()->addSuspicion($suspect, $witness);
    }

    public function getDurationOfPreliminaryProceedings() {
        return new \DateInterval("PT{$this->durationOfPreliminaryProceedingsInMinutes}M");
    }

    public function getRequiredPositiveSuspicionRate() {
        return $this->requiredPositiveSuspicionRate;
    }

    public function finish() {
        $this->finished = true;
    }

}