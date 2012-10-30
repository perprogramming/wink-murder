<?php

namespace WinkMurder\Bundle\GameBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * @Route("/game")
 */
class GameController extends BaseController {

    /**
     * @Route("/")
     */
    public function indexAction() {
        $game = $this->getCurrentGame();
        if ($game && $game->isStarted()) {
            if ($this->getAuthenticatedPlayer()) {
                return $this->redirect($this->generateUrl('winkmurder_game_game_status'));
            } else {
                return $this->redirect($this->generateUrl('winkmurder_game_game_players'));
            }
        } else {
            return $this->redirect($this->generateUrl('winkmurder_game_game_story'));
        }
    }

    /**
     * @Route("/status/")
     * @Template
     */
    public function statusAction() {
        return array('game' => $this->getCurrentGame());
    }

    /**
     * @Route("/players/")
     * @Template
     */
    public function playersAction() {
        return array('game' => $this->getCurrentGame());
    }

    /**
     * @Route("/rules/")
     * @Template
     */
    public function rulesAction() {
        return array('game' => $this->getCurrentGame());
    }

    /**
     * @Route("/story/")
     * @Template
     */
    public function storyAction() {
        return array('game' => $this->getCurrentGame());
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
        return $this->redirect($this->generateUrl('winkmurder_game_game_players'));
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
        return $this->redirect($this->generateUrl('winkmurder_game_game_status'));
    }

}