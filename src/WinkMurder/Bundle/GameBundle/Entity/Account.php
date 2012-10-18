<?php

namespace WinkMurder\Bundle\GameBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="WinkMurder\Bundle\GameBundle\Entity\AccountRepository")
 */
class Account implements UserInterface {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\OneToOne(targetEntity="Player", cascade={"PERSIST"})
     * @ORM\JoinColumn(name="player_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $player;

    /**
     * @ORM\Column
     */
    protected $username;

    public function __construct(Player $player) {
        $this->player = $player;
        $this->username = md5(uniqid() . time() . rand(0, 1000));
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

    public function getUsername() {
        return $this->username;
    }

    public function getPassword() {
        return null;
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