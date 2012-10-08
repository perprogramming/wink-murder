<?php

namespace WinkMurder\Bundle\GameBundle\Twig;

use WinkMurder\Bundle\GameBundle\Entity\Player;
use WinkMurder\Bundle\GameBundle\Entity\Admin;
use WinkMurder\Bundle\GameBundle\Entity\Murderer;
use Symfony\Component\Security\Core\SecurityContextInterface;

class Extension extends \Twig_Extension {

    protected $roleLabels;
    protected $securityContext;

    public function __construct(array $roleLabels) {
        $this->roleLabels = $roleLabels;
    }

    public function getName() {
        return 'wink_murder';
    }

    public function getGlobals() {
        return array(
            'roleLabels' => $this->roleLabels
        );
    }

}