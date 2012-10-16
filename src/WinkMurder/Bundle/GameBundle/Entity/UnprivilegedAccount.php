<?php

namespace WinkMurder\Bundle\GameBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class UnprivilegedAccount extends Account {

    public function __construct(Player $player) {
        parent::__construct($player);

        $this->username = md5(uniqid() . time() . rand(0, 1000));
        $this->password = '';
    }

    public function getRoles() {
        return array('ROLE_PLAYER');
    }

}