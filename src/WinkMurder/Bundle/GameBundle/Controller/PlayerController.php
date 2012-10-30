<?php

namespace WinkMurder\Bundle\GameBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;


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

    /**
     * @Route("/commit-murder/{id}/")
     * @Template
     */
    public function commitMurderAction($id) {
        if ($victim = $this->getCurrentGame()->findPlayer($id)) {
            if ($this->getAuthenticatedPlayer()->canMurder($victim)) {
                return array('victim' => $victim);
            }
        }
        return $this->redirect($this->generateUrl('winkmurder_game_player_index'));
    }

    /**
     * @Route("/commit-murder/{id}/confirm/")
     * @Method("POST")
     */
    public function confirmCommitMurderAction($id) {
        try {
            $this->getAuthenticatedPlayer()->murder(
                $this->getCurrentGame()->findPlayer($id)
            );
            $this->getEntityManager()->flush();
        } catch (\Exceptions $e) {
        }
        return $this->redirect($this->generateUrl('winkmurder_game_player_index'));
    }

}