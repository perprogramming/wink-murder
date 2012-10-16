<?php

namespace WinkMurder\Bundle\GameBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="WinkMurder\Bundle\GameBundle\Entity\AccountRepository")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discr")
 * @ORM\DiscriminatorMap({"admin" = "AdminAccount", "unprivileged" = "UnprivilegedAccount"})
 */
abstract class Account implements UserInterface {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\OneToOne(targetEntity="Player")
     */
    protected $player;

    /**
     * @ORM\Column
     * @Assert\NotBlank
     */
    protected $username;

    /**
     * @ORM\Column
     * @Assert\NotBlank
     */
    protected $password;

    public function __construct(Player $player) {
        $this->player = $player;
    }

    public function getId() {
        return $this->id;
    }

    public function getRoles() {
        return array('ROLE_PLAYER');
    }

    public function getPlayer() {
        return $this->player;
    }

    public function setPlayer(Player $player = null) {
        $this->player = $player;
    }

    public function getUsername() {
        return $this->username;
    }

    public function getPassword() {
        return $this->password;
    }

    public function equals(UserInterface $user) {
        return $this->getUsername() == $user->getUsername();
    }

    public function getSalt() {
        return null;
    }

    public function eraseCredentials() {
    }

}