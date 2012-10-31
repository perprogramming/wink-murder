<?php

namespace WinkMurder\Bundle\GameBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use WinkMurder\Bundle\GameBundle\Entity\Hash\Hashable;

/**
 * @ORM\Entity
 */
class Player implements Hashable {

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

    /**
     * @ORM\ManyToOne(targetEntity="MannerOfDeath", inversedBy="players")
     * @ORM\JoinColumn(name="mannerOfDeath_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $mannerOfDeath;

    /**
     * @ORM\ManyToOne(targetEntity="Murder", inversedBy="likes")
     * @ORM\JoinColumn(name="favoriteMurder_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $favoriteMurder;

    /**
     * @ORM\OneToOne(targetEntity="Account", mappedBy="player")
     */
    protected $account;

    public function __construct(Game $game, Photo $photo, MannerOfDeath $mannerOfDeath) {
        $this->game = $game;
        $this->photo = $photo;
        $this->mannerOfDeath = $mannerOfDeath;
        $this->mannerOfDeath->addPlayer($this);
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
            $this->game->checkKill($victim, $this);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function hasSuspicion() {
        return $this->game->hasSuspicion($this);
    }

    public function getSuspicion() {
        return $this->game->getSuspicion($this);
    }

    public function canSuspect(Player $suspect = null) {
        return $this->game->canSuspect($this, $suspect);
    }

    public function suspect(Player $suspect) {
        $this->game->suspect($this, $suspect);
    }

    public function murder(Player $victim) {
        $this->game->kill($victim);
    }

    public function likesMurder(Player $player) {
        return $this->favoriteMurder && ($this->favoriteMurder->getVictim() === $player);
    }

    public function likeMurder(Player $player) {
        if ($this->canLikeMurder($player)) {
            $this->favoriteMurder = $this->game->findMurderByPlayer($player);
        }
    }

    public function canLikeMurder(Player $player) {
        return ($player->isDead() && ($player !== $this) && !$this->likesMurder($player));
    }

    public function setMannerOfDeath($mannerOfDeath) {
        $this->mannerOfDeath = $mannerOfDeath;
    }

    public function getMannerOfDeath() {
        return $this->mannerOfDeath;
    }

    public function getFavoriteMurder() {
        return $this->favoriteMurder;
    }

    public function setAccount(Account $account) {
        $this->account = $account;
    }

    public function getAccount() {
        return $this->account;
    }

    public function getHashValues() {
        return array(
            'id' => $this->id,
            'game' => $this->game,
            'photo' => $this->photo,
            'murder' => $this->murder,
            'mannerOfDeath' => $this->mannerOfDeath,
            'favoriteMurder' => $this->favoriteMurder
        );
    }

}