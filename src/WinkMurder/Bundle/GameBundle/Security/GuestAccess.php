<?php

namespace WinkMurder\Bundle\GameBundle\Security;

use Symfony\Component\Security\Core\User\UserInterface;
use WinkMurder\Bundle\GameBundle\Entity\Player;

class GuestAccess implements UserInterface {

    protected $username;
    protected $password;
    protected $player;

    public function __construct($password, Player $player = null) {
        $this->password = $password;
        $this->setPlayer($player);
    }

    public function setPlayer(Player $player = null) {
        $this->player = $player;
        if ($player) {
            $this->username = 'guest-access-player:' . $player->getId();
        } else {
            $this->username = 'admin';
        }
    }

    public function getPlayer() {
        return $this->player;
    }

    public function getRoles() {
        return array('ROLE_GUEST', 'ROLE_PLAYER');
    }

    public function getPassword() {
        return $this->password;
    }

    public function getSalt() {
        return null;
    }

    public function getUsername() {
        return $this->username;
    }

    public function eraseCredentials() {
    }

}