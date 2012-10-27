<?php

namespace WinkMurder\Bundle\GameBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use WinkMurder\Bundle\GameBundle\Entity\Hash\Hashable;
use WinkMurder\Bundle\GameBundle\Entity\Hash\Hash;

/**
 * @ORM\Entity(repositoryClass="WinkMurder\Bundle\GameBundle\Entity\GameRepository")
 */
class Game implements Hashable {

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
     * @ORM\OrderBy({"id" = "ASC"})
     */
    protected $players;

    /**
     * @ORM\ManyToOne(targetEntity="Photo")
     */
    protected $murdererPhoto;

    /**
     * @ORM\OneToMany(targetEntity="Murder", mappedBy="game", cascade={"PERSIST", "REMOVE"}, orphanRemoval=true)
     * @ORM\OrderBy({"timeOfOffense" = "ASC"})
     */
    protected $murders;

    /**
     * @ORM\Column(type="integer")
     */
    protected $requiredPositiveSuspicionRate = 50   ;

    /**
     * @ORM\Column(type="integer")
     */
    protected $durationOfPreliminaryProceedingsInMinutes = 10;

    /**
     * @ORM\Column(type="integer")
     */
    protected $requiredMurders = 15;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $finished = false;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $started = false;

    public function __construct(PhotoSet $photoSet) {
        $this->photoSet = $photoSet;
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
        $players = $this->players;
        return array_filter($this->photoSet->getPhotos(), function(Photo $photo) use ($players) {
            foreach ($players as $player) {
                if ($player->getPhoto() === $photo) {
                    return false;
                }
            }
            return true;
        });
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

    public function setMurdererPhoto(Photo $photo = null) {
        $this->murdererPhoto = $photo;
    }

    public function getMurdererPhoto() {
        return $this->murdererPhoto;
    }

    public function getMurderer() {
        foreach ($this->players as $player) {
            if ($player->getPhoto() == $this->murdererPhoto) {
                return $player;
            }
        }
    }

    public function isMurderer(Player $player) {
        return $this->getMurderer() === $player;
    }

    public function canBeKilled(Player $victim) {
        try {
            $this->checkKill($victim);
            return true;
        } catch (\Exception $e) {}
        return false;
    }

    public function getAliveOtherPlayers(Player $player) {
        return array_filter($this->getAlivePlayers(), function(Player $other) use ($player) {
            return $other !== $player;
        });
    }

    public function getAlivePlayers() {
        return array_filter($this->players->toArray(), function(Player $player) {
            return !$player->isDead();
        });
    }

    public function checkKill(Player $victim, Player $murderer = null) {
        if ($this->hasMurdererWon()) throw new \Exception("The murderer has already won.");
        if ($murderer && !$murderer->isMurderer()) throw new \Exception("Player {$murderer->getName()} is not the murderer.");
        if ($victim->isDead()) throw new \Exception("Player {$victim->getName()} is already dead.");
        if ($murderer && ($murderer === $victim)) throw new \Exception("Player {$murderer->getName()} cannot murder himself.");
        if ($victim->isMurderer()) throw new \Exception("The Murderer cannot be killed.");
        if ($this->arePreliminaryProceedingsOngoing()) throw new \Exception("The preliminary proceedings of the last murder are still ongoing.");
        if ($this->isMurdererIdentified()) throw new \Exception("The murderer is identified.");
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

    public function getSuspicion(Player $witness) {
        return $this->getLatestMurder()->getSuspicion($witness);
    }

    public function addSuspicion(Player $suspect, Player $witness) {
        $this->getLatestMurder()->addSuspicion($suspect, $witness);
    }

    public function isMurdererIdentified() {
        if ($latestMurder = $this->getLatestMurder()) {
            return $latestMurder->isClearedUp();
        }
        return false;
    }

    public function arePreliminaryProceedingsOngoing() {
        if ($latestMurder = $this->getLatestMurder()) {
            return !$latestMurder->arePreliminaryProceedingsDiscontinued();
        }
        return false;
    }

    public function getEndOfPreliminaryProceedings() {
        return $this->getLatestMurder()->getEndOfPreliminaryProceedings();
    }

    public function getDurationOfPreliminaryProceedings() {
        return new \DateInterval("PT{$this->durationOfPreliminaryProceedingsInMinutes}M");
    }

    public function getRequiredPositiveSuspicionRate() {
        return $this->requiredPositiveSuspicionRate;
    }

    public function isStarted() {
        return $this->started;
    }

    public function isFinished() {
        return $this->finished;
    }

    public function start() {
        $this->started = true;
    }

    public function finish() {
        $this->finished = true;
    }

    public function hasMurdererWon() {
        $numberOfUnsolvedMurders = 0;
        foreach ($this->murders as $murder) {
            if ($murder->isUnsolved()) {
                $numberOfUnsolvedMurders++;
            }
        }
        return $numberOfUnsolvedMurders >= $this->requiredMurders;
    }

    public function hasMurdererLost() {
        return $this->getLatestMurder()->isClearedUp();
    }

    public function getStatus() {
        if ($latestMurder = $this->getLatestMurder()) {
            if ($this->hasMurdererWon()) {
                return 'murdererWon';
            } elseif ($this->hasMurdererLost()) {
                return 'murdererLost';
            } elseif ($latestMurder->arePreliminaryProceedingsDiscontinued()) {
                return 'proceedingsDiscontinued';
            } else {
                return 'proceedingsOngoing';
            }
        } else {
            return 'noMurderYet';
        }
    }

    public function getHash() {
        return new Hash($this);
    }

    public function getHashValues() {
        return array(
            'id' => $this->id,
            'photoSet' => $this->photoSet,
            'players' => $this->players,
            'murdererPhoto' => $this->murdererPhoto,
            'murders' => $this->murders,
            'requiredPositiveSuspicionRate' => $this->requiredPositiveSuspicionRate,
            'durationOfPreliminaryProceedingsInMinutes' => $this->durationOfPreliminaryProceedingsInMinutes,
            'requiredMurders' => $this->requiredMurders,
            'started' => $this->started,
            'finished' => $this->finished
        );
    }

    public function setDurationOfPreliminaryProceedingsInMinutes($durationOfPreliminaryProceedingsInMinutes) {
        $this->durationOfPreliminaryProceedingsInMinutes = $durationOfPreliminaryProceedingsInMinutes;
    }

    public function getDurationOfPreliminaryProceedingsInMinutes() {
        return $this->durationOfPreliminaryProceedingsInMinutes;
    }

    public function setRequiredMurders($requiredMurders) {
        $this->requiredMurders = $requiredMurders;
    }

    public function getRequiredMurders() {
        return $this->requiredMurders;
    }

    public function setRequiredPositiveSuspicionRate($requiredPositiveSuspicionRate) {
        $this->requiredPositiveSuspicionRate = $requiredPositiveSuspicionRate;
    }

}