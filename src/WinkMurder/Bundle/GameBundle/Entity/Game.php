<?php

namespace WinkMurder\Bundle\GameBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="WinkMurder\Bundle\GameBundle\Entity\GameRepository")
 * @ORM\Table(name="Game", indexes={@ORM\Index(name="finished", columns={"finished", "id"})})
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
     * @ORM\ManyToOne(targetEntity="Photo")
     * @ORM\JoinColumn(name="murdererPhoto_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $murdererPhoto;

    /**
     * @ORM\OneToMany(targetEntity="Murder", mappedBy="game", cascade={"PERSIST", "REMOVE"}, orphanRemoval=true)
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

    /** @return PhotoSet */
    public function getPhotoSet() {
        return $this->photoSet;
    }

    /** @return Photo[] */
    public function getPhotos() {
        $photos = $this->photoSet->getPhotos();
        usort($photos, function($photoA, $photoB) {
            return strcmp($photoA->getTitle(), $photoB->getTitle());
        });
        return $photos;
    }

    /** @return Photo[] */
    public function getUnusedPhotos() {
        $players = $this->players;
        return array_filter($this->getPhotos(), function(Photo $photo) use ($players) {
            foreach ($players as $player) {
                if ($player->getPhoto() === $photo) {
                    return false;
                }
            }
            return true;
        });
    }

    /** @return Photo */
    public function findUnusedPhoto($id) {
        foreach ($this->getUnusedPhotos() as $unusedPhoto) {
            if ($id == $unusedPhoto->getId()) {
                return $unusedPhoto;
            }
        }
    }

    /** @return Player */
    public function findPlayer($id) {
        foreach ($this->players as $player) {
            if ($player->getId() == $id) {
                return $player;
            }
        }
    }

    /** @return Murder */
    public function findMurderByPlayer(Player $player) {
        foreach ($this->murders as $murder) {
            if ($murder->getVictim() == $player) {
                return $murder;
            }
        }
    }

    /** @return Murder[] */
    public function getMurders() {
        $murders = $this->murders->toArray();
        usort($murders, function(Murder $murderA, Murder $murderB) {
            return intval($murderB->getTimeOfOffense()->format('U')) - intval($murderA->getTimeOfOffense()->format('U'));
        });
        return $murders;
    }

    /** @return Players[] */
    public function getPlayers() {
        $players = $this->players->toArray();
        usort($players, function($playerA, $playerB) {
            return strcmp($playerA->getName(), $playerB->getName());
        });
        return $players;
    }

    /** @return Murder */
    public function getLatestMurderWithPreliminaryProceedingsOngoing() {
        if ($murder = $this->getLatestMurder()) {
            if (!$murder->arePreliminaryProceedingsDiscontinued()) {
                return $murder;
            }
        }
    }

    /** @return Murder */
    public function getLatestMurderWithPreliminaryProceedingsDiscontinued() {
        foreach ($this->getMurders() as $murder) {
            if ($murder->arePreliminaryProceedingsDiscontinued()) {
                return $murder;
            }
        }
    }

    /** @return Murder[] */
    public function getMurdersWithSuspicionOrPreliminaryProceedingsDiscontinued(Player $player) {
        return array_filter($this->getMurders(), function(Murder $murder) use ($player) {
            return $murder->arePreliminaryProceedingsDiscontinued() || $murder->hasSuspicion($player);
        });
    }

    /** @return Murder[] */
    public function getMurdersWithPreliminaryProceedingsDiscontinued() {
        return array_filter($this->getMurders(), function(Murder $murder) {
            return $murder->arePreliminaryProceedingsDiscontinued();
        });
    }

    /** @return Player */
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

    /** @return Photo */
    public function getMurdererPhoto() {
        return $this->murdererPhoto;
    }

    /** @return Player */
    public function getMurderer() {
        return $this->findPlayerByPhoto($this->murdererPhoto);
    }

    /** @return Photos */
    public function getPhotosWithoutAccount() {
        $game = $this;
        return array_filter($this->getPhotos(), function(Photo $photo) use ($game) {
            if ($player = $game->findPlayerByPhoto($photo)) {
                return !$player->getAccount();
            } else {
                return true;
            }
        });
    }

    /** @return Photo */
    public function findPlayerByPhoto(Photo $photo = null) {
        foreach ($this->players as $player) {
            if ($player->getPhoto() == $photo) {
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

    /** @return Players[] */
    public function getOtherPlayers(Player $player = null) {
        return array_filter($this->getPlayers(), function(Player $other) use ($player) {
            return $other !== $player;
        });
    }

    /** @return Players[] */
    public function getAliveOtherPlayers(Player $player = null) {
        return array_filter($this->getAlivePlayers(), function(Player $other) use ($player) {
            return $other !== $player;
        });
    }

    /** @return Players[] */
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

    public function kill(Player $player, Player $murderer = null, $time = null) {
        $this->checkKill($player, $murderer);
        $this->murders->add(new Murder($this, $player, $time ?: new \DateTime('now')));
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
        return reset($this->getMurders());
    }

    public function canSuspect(Player $witness, Player $suspect = null) {
        if (!$this->getLatestMurderWithPreliminaryProceedingsOngoing()) return false;
        if ($witness->isDead()) return false;
        if ($this->hasSuspicion($witness)) return false;
        if ($suspect && $suspect->isDead()) return false;
        return true;
    }

    public function hasSuspicion(Player $witness) {
        return $this->getLatestMurder()->hasSuspicion($witness);
    }

    /** @return Suspicion */
    public function getSuspicion(Player $witness) {
        return $this->getLatestMurder()->getSuspicion($witness);
    }

    public function suspect(Player $witness, Player $suspect) {
        if ($this->canSuspect($witness, $suspect)) {
            $this->getLatestMurder()->addSuspicion($witness, $suspect);
        }
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

    /** @return \DateTime */
    public function getEndOfPreliminaryProceedings() {
        return $this->getLatestMurder()->getEndOfPreliminaryProceedings();
    }

    /** @return \DateInterval */
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
        if ($this->getLatestMurder()) {
        $numberOfUnsolvedMurders = 0;
        foreach ($this->murders as $murder) {
            if ($murder->isUnsolved()) {
                $numberOfUnsolvedMurders++;
            }
        }
        return $numberOfUnsolvedMurders >= (min($this->requiredMurders, (count($this->getOtherPlayers($this->getMurderer())))));
    }
        return false;
    }

    public function getAccounts() {
        $accounts = array();
        foreach ($this->getPlayers() as $player) {
            if ($account = $player->getAccount()) {
                $accounts[] = $account;
            }
        }
        return $accounts;
    }

    public function hasMurdererLost() {
        if ($this->getLatestMurder()) {
        return $this->getLatestMurder()->isClearedUp();
    }
        return false;
    }

    public function getStatus() {
            if ($this->hasMurdererWon()) {
                return 'murdererWon';
            } elseif ($this->hasMurdererLost()) {
                return 'murdererLost';
        } elseif ($this->getLatestMurder()) {
            return 'murdererStillAtLarge';
        } else {
            return 'noMurderYet';
        }
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

    /** @return Murder */
    public function getMostPopularDeath() {
        $mostPopularDeath = null;
        foreach ($this->murders as $murder) {
            if ($numberOfLikes = count($murder->getLikes())) {
                if (!$mostPopularDeath || ($numberOfLikes >= count($mostPopularDeath->getLikes()))) {
                    $mostPopularDeath = $murder;
                }
            }
        }
        return $mostPopularDeath;
    }

}