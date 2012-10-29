<?php

namespace WinkMurder\Bundle\GameBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;
use WinkMurder\Bundle\GameBundle\Entity\Hash\Hashable;

/**
 * @ORM\Entity(repositoryClass="WinkMurder\Bundle\GameBundle\Entity\MannerOfDeathRepository")
 */
class MannerOfDeath implements Hashable {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\OneToMany(targetEntity="Player", mappedBy="mannerOfDeath", cascade={"PERSIST"})
     */
    protected $players;

    /**
     * @ORM\Column
     * @Gedmo\Translatable
     */
    protected $name = '';

    /**
     * @Gedmo\Locale
     */
    protected $locale;

    public function __construct() {
        $this->players = new ArrayCollection();
    }

    public function getId() {
        return $this->id;
    }

    public function getPlayers() {
        return $this->players->toArray();
    }

    public function addPlayer(Player $player) {
        $this->players->add($player);
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function getName() {
        return $this->name;
    }

    public function setTranslatableLocale($locale) {
        $this->locale = $locale;
    }

    public function getHashValues() {
        return array(
            'id' => $this->id,
            'name' => $this->name
        );
    }

}