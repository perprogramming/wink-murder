<?php

namespace WinkMurder\Bundle\GameBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class StartController extends BaseController {

    /**
     * @Route("/")
     */
    public function indexAction() {
        return $this->redirect($this->generateUrl('winkmurder_game_game_index'));
    }

}