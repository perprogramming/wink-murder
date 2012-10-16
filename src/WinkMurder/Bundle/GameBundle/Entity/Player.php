<?php

namespace WinkMurder\Bundle\GameBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="WinkMurder\Bundle\GameBundle\Entity\PlayerRepository")
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="flickrId",columns={"flickrId"})})
 */
class Player {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\Column
     */
    protected $flickrId;

    /**
     * @ORM\Column
     */
    protected $name;

    /**
     * @ORM\Column
     */
    protected $imageUrl;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $murderer = false;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $dead = false;

    public function __construct($flickrId) {
        $this->flickrId = $flickrId;
    }

    public function getId() {
        return $this->id;
    }

    public function getFlickrId() {
        return $this->flickrId;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function getName() {
        return $this->name;
    }

    public function setImageUrl($imageUrl) {
        $this->imageUrl = $imageUrl;
    }

    public function getImageUrl() {
        return $this->imageUrl;
    }

    public function isMurderer() {
        return $this->murderer === true;
    }

    public function setMurderer($value) {
        return $this->murderer = (boolean) $value;
    }

    public function isDead() {
        return $this->dead === true;
    }

    public function setDead($value) {
        $this->dead = $value;
    }

    public function mayMurder(Player $player) {
        if (!$this->isMurderer()) return false;
        if ($player->isMurderer()) return false;
        if ($player->isDead()) return false;
        return true;
    }

    public function murder(Player $player) {
        if (!$this->isMurderer())
            throw new \Exception("{$this->name} is not a murderer.");

        if ($player->isMurderer())
            throw new \Exception("{$player->name} is a murderer and cannot be killed.");

        if ($player->isDead())
            throw new \Exception("{$player->name} is already dead.");

        $player->setDead(true);
    }

}