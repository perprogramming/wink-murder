<?php

namespace WinkMurder\Bundle\GameBundle\Twig;

use WinkMurder\Bundle\GameBundle\Entity\Player;
use WinkMurder\Bundle\GameBundle\Entity\Admin;
use WinkMurder\Bundle\GameBundle\Entity\Murderer;

class Extension extends \Twig_Extension {

    public function getName() {
        return 'wink_murder';
    }

    public function getTests() {
        return array(
            'admin' => new \Twig_Test_Method($this, 'isAdmin'),
            'murderer' => new \Twig_Test_Method($this, 'isMurderer'),
        );
    }

    public function isAdmin(Player $player) {
        return $player instanceof Admin;
    }

    public function isMurderer(Player $player) {
        return $player instanceof Murderer;
    }

}