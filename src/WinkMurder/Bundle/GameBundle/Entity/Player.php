<?php

namespace WinkMurder\Bundle\GameBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="WinkMurder\Bundle\GameBundle\Entity\PlayerRepository")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({"player" = "Player", "admin" = "Admin", "murderer" = "Murderer"})
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
    protected $name;

    /**
     * @ORM\Column
     */
    protected $password;

    /**
     * @ORM\Column
     */
    protected $avatarPath = '';

    public function __construct($password) {
        $this->password = $password;
    }

    public function getId() {
        return $this->id;
    }

    public function getPassword() {
        return $this->password;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function getName() {
        return $this->name;
    }

    public function setAvatarPath($avatarPath) {
        $this->avatarPath = $avatarPath;
    }

    public function getAvatarPath() {
        return $this->avatarPath;
    }

}