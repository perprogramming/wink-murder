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
     */
    public function statusAction() {
        $game = $this->getCurrentGame();
        return $this->render(
            'WinkMurderGameBundle:Game:' . $game->getStatus() . '.html.twig',
            array('game' => $game)
        );
    }

    /**
     * @Route("/commit-murder/{id}")
     */
    public function commitMurderAction($id) {
        try {
            $this->getAuthenticatedPlayer()->murder(
                $this->getCurrentGame()->findPlayer($id)
            );
            $this->getEntityManager()->flush();
        } catch (\Exceptions $e) {
        }
        return $this->redirect($this->generateUrl('winkmurder_game_game_status'));
    }

    /**
     * @Route("/suspect/{id}/")
     */
    public function suspectAction($id) {
        try {
            $this->getAuthenticatedPlayer()->suspect(
                $this->getCurrentGame()->findPlayer($id)
            );
            $this->getEntityManager()->flush();
        } catch (\Exception $e) {
        }
        return $this->redirect($this->generateUrl('winkmurder_game_game_status'));
    }

}