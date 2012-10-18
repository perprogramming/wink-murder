<?php

namespace WinkMurder\Bundle\GameBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * @Route("/game")
 */
class GameController extends BaseController {

    /**
     * @Route("/")
     * @Template
     */
    public function statusAction() {
        return array();
    }

    /**
     * @Route("/commit-murder/{id}")
     */
    public function commitMurderAction($id) {
        $this->getAuthenticatedPlayer()->murder(
            $this->getCurrentGame()->findPlayer($id)
        );
        $this->getEntityManager()->flush();
        return $this->redirect($this->generateUrl('winkmurder_game_game_status'));
    }

    /**
     * @Route("/suspect/{id}/")
     */
    public function suspectAction($id) {
        $this->getAuthenticatedPlayer()->suspect(
            $this->getCurrentGame()->findPlayer($id)
        );
        $this->getEntityManager()->flush();
        return $this->redirect($this->generateUrl('winkmurder_game_game_status'));
    }

}