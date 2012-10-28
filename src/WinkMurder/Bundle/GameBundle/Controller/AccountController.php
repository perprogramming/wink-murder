<?php

namespace WinkMurder\Bundle\GameBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class AccountController extends BaseController {



    /**
     * @Route("/login/")
     * @Template
     */
    public function loginAction() {
        if ($this->getAuthenticatedPlayer()) {
            return $this->redirect($this->generateUrl('winkmurder_game_profile_show'));
        } else {
            $game = $this->getCurrentGame();
            if ($game && $game->isStarted()) {
                return array('game' => $this->getCurrentGame());
            } else {
                return $this->redirect($this->generateUrl('winkmurder_game_start_index'));
            }
        }
    }

    /**
     * @Route("/login/check/")
     */
    public function loginCheckAction() {
    }

}