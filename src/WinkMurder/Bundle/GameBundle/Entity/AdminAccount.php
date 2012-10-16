<?php

namespace WinkMurder\Bundle\GameBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class AdminAccount extends Account {

    public function setPassword($password) {
        $this->password = $password;
    }

    public function setUsername($username) {
        $this->username = $username;
    }

    public function getRoles() {
        return array('ROLE_ADMIN');
    }

}