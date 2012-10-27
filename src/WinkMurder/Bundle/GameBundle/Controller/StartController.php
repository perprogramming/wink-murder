<?php

namespace WinkMurder\Bundle\GameBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class StartController extends BaseController {

    /**
     * @Route("/")
     * @Template
     */
    public function indexAction() {
        $game = $this->getCurrentGame();
        if ($game && $game->isStarted()) {
            return $this->redirect($this->generateUrl('winkmurder_game_profile_show'));
        } else {
            return array();
        }
    }

}