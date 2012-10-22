<?php

namespace WinkMurder\Bundle\GameBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * @Route("/players")
 */
class PlayerController extends BaseController {

    /**
     * @Route("/")
     * @Template
     */
    public function indexAction() {
        return array('players' => $this->getCurrentGame()->getPlayers());
    }

}