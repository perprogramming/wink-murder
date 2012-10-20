<?php

namespace WinkMurder\Bundle\GameBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="WinkMurder\Bundle\GameBundle\Entity\MannerOfDeathRepository")
 */
class MannerOfDeath {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\OneToMany(targetEntity="Player", mappedBy="mannerOfDeath")
     */
    protected $players;

    /**
     * @ORM\Column
     * @Assert\NotBlank
     */
    protected $name = '';
    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank
     */
    protected $briefing = '';

    public function __construct() {
        $this->players = new ArrayCollection();
    }

    public function getId() {
        return $this->id;
    }

    public function getPlayers() {
        return $this->players;
    }

    public function addPlayer(Player $player) {
        $this->players->add($player);
    }

    public function setBriefing($briefing) {
        $this->briefing = $briefing;
    }

    public function getBriefing() {
        return $this->briefing;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function getName() {
        return $this->name;
    }

}